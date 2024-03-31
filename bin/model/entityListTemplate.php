<?php

namespace Components\Views\Admin\SubFolder;

use Components\Models\ModelFolder\EntityNameModel;
use Components\Models\Pagination\PaginationModel;
use Components\Services\Messages\MessageService;
use Components\Views\UI\Modal\Modal;
use Viewi\Components\BaseComponent;
use Viewi\Components\DOM\DomEvent;
use Viewi\Components\Http\HttpClient;
use Viewi\Components\Attributes\Middleware;
use Components\Guards\AdminGuard;

#[Middleware([AdminGuard::class])]
class EntityNameList extends BaseComponent
{
    /**
     * 
     * @var EntityNameModel[]
     */
    public array $items = [];
    public PaginationModel $paging;
    public ?EntityNameModel $selectedItem = null;
    public ?Modal $deleteModal = null;
    private string $searchText = '';

    public function __construct(
        private HttpClient $http,
        private MessageService $messages
    ) {
        $this->paging = new PaginationModel(1, 10, 0);
    }

    public function init()
    {
        $this->getData();
    }

    private function getData()
    {
        $searchEncoded = urlencode($this->searchText);
        $this->http->get("/api/admin/entityName?page={$this->paging->page}&size={$this->paging->size}&search={$searchEncoded}")
            ->then(function ($posts) {
                $this->items = $posts['list'];
                $this->paging->setTotal($posts['total']);
            }, function () {
                // error
            });
    }

    public function onSearch(DomEvent $event)
    {
        $this->searchText = $event->target->value;
        $this->getData();
    }

    public function onPageChange()
    {
        $this->getData();
    }

    public function onDelete(EntityNameModel $item)
    {
        $this->selectedItem = $item;
        $this->deleteModal->title = "Are you sure you want to delete '{$item->Name}' with Id {$item->Id}?";
        $this->deleteModal->show = true;
    }

    public function onDeleteCancel()
    {
        $this->selectedItem = null;
    }

    public function deleteSelected()
    {
        $this->http->delete("/api/admin/entityName/{$this->selectedItem->Id}")->then(function () {
            $this->messages->success('EntityName has been successfully deleted', null, 5000);
            $this->getData();
        }, function ($error) {
            // error
            echo $error;
            $this->messages->error('EntityName deletion has failed', null, 5000);
        });
    }

    // TODO: trait
    public function formatDate(int $milliseconds)
    {
        $seconds = $milliseconds / 1000000;
        return gmdate('Y-m-d', (int)$seconds); //  H:i:s
    }

    public function getDecimalPrice(int $price)
    {
        return number_format($price / 100, 2, ',', ' ');
    }
}
