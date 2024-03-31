<?php

namespace Components\Views\Admin\SubFolder;

use Components\Models\ModelFolder\EntityNameModel;
use Components\Models\ModelFolder\EntityNameValidation;
use Components\Models\Media\PictureModel;
use Components\Services\Messages\MessageService;
use Components\Views\UI\Forms\ActionForm;
use Components\Views\UI\Validation\ValidationMessage;
use Viewi\Components\BaseComponent;
use Viewi\Components\DOM\DomEvent;
use Viewi\Components\Http\HttpClient;
use Viewi\Components\Routing\ClientRoute;
use Viewi\Components\Attributes\Middleware;
use Components\Guards\AdminGuard;

#[Middleware([AdminGuard::class])]
class EntityNameEdit extends BaseComponent
{
    public ?EntityNameModel $item = null;
    public int $state = 0; // 0 wait, 1 saving, 2 saved
    public bool $createMode = false;
    public ?EntityNameValidation $validation = null;
    private ?ActionForm $actionForm = null;
    public ?ValidationMessage $generalMessages = null;

    public function __construct(
        private int $id,
        private HttpClient $http,
        private MessageService $messages,
        private ClientRoute $route
    ) {
    }

    public function init()
    {
        if ($this->id > 0) {
            //edit
            $this->http->get("/api/admin/entityName/{$this->id}")
                ->then(function (EntityNameModel $page) {
                    $this->item = $page;
                    $this->validation = new EntityNameValidation($this->item);
                }, function () {
                    // error
                });
        } else {
            // create
            $this->createMode = true;
            $this->item = new EntityNameModel();
            $this->validation = new EntityNameValidation($this->item);
        }
    }

    public function onSave(DomEvent $event)
    {
        $event->preventDefault();
        if (!$this->actionForm->validate()) {
            return;
        }
        $this->state = 1;
        $this->http->request(
            $this->createMode ? 'post' : 'put',
            $this->createMode ? '/api/admin/entityName' : "/api/admin/entityName/{$this->id}",
            $this->item
        )
            ->then(function (?EntityNameModel $post) {
                $this->stopLoading(2);
                if ($post !== null) {
                    $text = $this->createMode ? 'created' : 'saved';
                    $this->messages->success("EntityName was successfully $text.", null, 5000);
                    if ($this->createMode) {
                        $this->route->navigate("/admin/entityName/{$post->Id}");
                    } else {
                        $this->item = $post;
                        $this->validation = new EntityNameValidation($this->item);
                    }
                }
            }, function ($response) {
                $this->stopLoading(3);
                $this->handleResponse(true, $response);
            });
    }

    public function stopLoading(int $state)
    {
        <<<'javascript'
        setTimeout(() => $this.state = state, 500);
        setTimeout(() => $this.state = 0, 2500);
        javascript;
    }

    public function handleResponse(bool $hasError, $response = null)
    {
        if ($hasError) {
            if ($response['errors']) {
                $this->generalMessages->messages = $response['errors'];
                $this->messages->error($response['errors'][0], null, 5000);
            } else if ($response['message']) {
                $this->generalMessages->messages = [$response['message']];
                $this->messages->error($response['message'], null, 5000);
            } else {
                $this->generalMessages->messages = ['Saving has failed'];
                $this->messages->error('Saving has failed', null, 5000);
            }
            $this->generalMessages->show = true;
        }
    }

    public function removePicture(DomEvent $event)
    {
        $event->preventDefault();
        $this->item->PictureId = null;
        $this->item->PicturePath = null;
    }

    // file upload
    public function fileChanged(DomEvent $event)
    {
        $files = $event->target->files;
        // TODO: make utils
        <<<'javascript'
        files = Array.prototype.slice.call(files);
        javascript;
        if (count($files) > 0) {
            $file = $files[0];
            echo $file;
            $this->http->post(
                "/api/admin/picture/upload?fileName="
                    . urlencode($file->name)
                    . "&type="
                    . urlencode($file->type),
                $file
            )->then(
                function (PictureModel $picture) {
                    $this->item->PictureId = $picture->Id;
                    $this->item->PicturePath = $picture->Path;
                    $this->messages->success('File has been successfully uploaded', null, 5000);
                },
                function ($error) {
                    echo $error;
                    $this->messages->error('File upload has failed', null, 5000);
                }
            );
        }
    }
}
