<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Check Entity
 *
 * @property int $id
 * @property string|null $check_1
 * @property string|null $check_2
 * @property string|null $check_3
 * @property string|null $check_4
 * @property string|null $check_5
 * @property string|null $check_6
 * @property string|null $check_7
 * @property string|null $check_8
 * @property string|null $check_9
 * @property \Cake\I18n\FrozenTime|null $created
 */
class Check extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'check_1' => true,
        'check_2' => true,
        'check_3' => true,
        'check_4' => true,
        'check_5' => true,
        'check_6' => true,
        'check_7' => true,
        'check_8' => true,
        'check_9' => true,
        'created' => true,
        'title' => true,
        'user_id' => true,
       
    ];
}
