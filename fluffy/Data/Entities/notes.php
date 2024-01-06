// /**
// * @property-read UserEntity me
// */
abstract class BaseEntity
{
    public int $Id;

    public int $CreatedOn;
    public ?string $CreatedBy;

    public int $UpdatedOn;
    public ?string $UpdatedBy;

    // /** @property UserEntity $me */
    // private UserEntity $me;

    // public function &__get($property)
    // {
    //     echo 'Trying to get property ' . $property . PHP_EOL;
    //     if (!isset($this->{$property})) {
    //         $this->{$property} = new static;
    //     }
    //     return $this->{$property};
    // }
}