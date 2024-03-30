<?php

namespace Application\Controllers\Admin\SubFolder;

use Application\Data\Entities\SubFolder\EntityNameEntity;
use Application\Data\Entities\SubFolder\EntityNameEntityMap;
use Application\Data\Entities\Media\PictureEntity;
use Application\Data\Repositories\EntityNameRepository;
use Application\Data\Repositories\PictureRepository;
use Application\Services\Auth\MemberAuthorization;
use Components\Models\SubFolder\BaseEntityNameModel;
use Components\Models\SubFolder\EntityNameModel;
use Components\Models\SubFolder\EntityNameValidation;
use Fluffy\Controllers\BaseController;
use Fluffy\Data\Mapper\IMapper;

class EntityNameController extends BaseController
{
    function __construct(
        protected IMapper $mapper,
        protected EntityNameRepository $entityNames,
        protected PictureRepository $pictures,
        protected MemberAuthorization $auth
    ) {
    }

    public function List(int $page = 1, int $size = 10, ?string $search = null)
    {
        if (!$this->auth->authorizeAdminRequest()) {
            return $this->Forbidden();
        }
        $search = trim($search ?? '');
        $where = [];
        if ($search) {
            $where = [[EntityNameEntityMap::PROPERTY_Title, 'like', "%$search%"]];
        }
        $entities = $this->entityNames->search($where, [EntityNameEntityMap::PROPERTY_CreatedOn => 1], $page, $size);
        $models = array_map(fn ($entity) => $this->mapper->map(EntityNameModel::class, $entity), $entities['list']);
        return ['list' => $models, 'total' => $entities['total']];
    }

    public function Get(int $id)
    {
        if (!$this->auth->authorizeAdminRequest()) {
            return $this->Forbidden();
        }
        $entity = $this->entityNames->getById($id);
        if (!$entity) {
            return $this->NotFound();
        }
        /**
         * @var EntityNameModel $model
         */
        $model = $this->mapper->map(EntityNameModel::class, $entity);
        if ($model->PictureId !== null) {
            /**
             * @var ?PictureEntity $picture
             */
            $picture = $this->pictures->getById($model->PictureId);
            $model->PicturePath = $picture->Path;
        }
        return $model;
    }

    public function Update(int $id, BaseEntityNameModel $entityName)
    {
        if (!$this->auth->authorizeAdminRequest()) {
            return $this->Forbidden();
        }
        $validationMessages = [];
        $validationRules = (new EntityNameValidation($entityName))->getValidationRules();
        foreach ($validationRules as $property => $rules) {
            foreach ($rules as $validationRule) {
                $validationResult = $validationRule();
                if ($validationResult !== true) {
                    $validationMessages[] = $validationResult === false ? "Validation has failed for $property." : $validationResult;
                }
            }
        }
        if (count($validationMessages) > 0) {
            return $this->BadRequest($validationMessages);
        }
        // DB validations        
        $entityNameEntity = $this->entityNames->getById($id);
        if (!$entityNameEntity) {
            return $this->NotFound();
        }
        $entityNameEntity = $this->mapper->map(EntityNameEntity::class, $entityName, $entityNameEntity);
        $success = $this->entityNames->update($entityNameEntity);
        $model = null;
        if ($success) {
            /**
             * @var EntityNameModel $model
             */
            $model = $this->mapper->map(EntityNameModel::class, $entityNameEntity);
            if ($model->PictureId !== null) {
                /**
                 * @var ?PictureEntity $picture
                 */
                $picture = $this->pictures->getById($model->PictureId);
                $model->PicturePath = $picture->Path;
            }
        }
        return $model;
    }

    public function Delete(int $id)
    {
        if (!$this->auth->authorizeAdminRequest()) {
            return $this->Forbidden();
        }
        $entity = $this->entityNames->getById($id);
        if (!$entity) {
            return $this->NotFound();
        }
        $success = $this->entityNames->delete($entity);
        return $success;
    }

    public function Create(BaseEntityNameModel $entityName)
    {
        if (!$this->auth->authorizeAdminRequest()) {
            return $this->Forbidden();
        }
        $validationMessages = [];
        $validationRules = (new EntityNameValidation($entityName))->getValidationRules();
        foreach ($validationRules as $property => $rules) {
            foreach ($rules as $validationRule) {
                $validationResult = $validationRule();
                if ($validationResult !== true) {
                    $validationMessages[] = $validationResult === false ? "Validation has failed for $property." : $validationResult;
                }
            }
        }
        if (count($validationMessages) > 0) {
            return $this->BadRequest($validationMessages);
        }
        // DB validations       

        $entityNameEntity = $this->mapper->map(EntityNameEntity::class, $entityName);
        $success = $this->entityNames->create($entityNameEntity);
        return $success ? $this->mapper->map(EntityNameModel::class, $entityNameEntity) : null;
    }
}
