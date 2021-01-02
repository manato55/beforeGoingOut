<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Register Entity
 *
 * @property int $id
 * @property int|null $checks_id
 * @property string|null $register_1
 * @property string|null $register_2
 * @property string|null $register_3
 * @property string|null $register_4
 * @property string|null $register_5
 * @property string|null $register_6
 * @property string|null $register_7
 * @property string|null $register_8
 * @property string|null $register_9
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Check $check
 */
class Register extends Entity
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
        'check_id' => true,
        'register_1' => true,
        'register_2' => true,
        'register_3' => true,
        'register_4' => true,
        'register_5' => true,
        'register_6' => true,
        'register_7' => true,
        'register_8' => true,
        'register_9' => true,
        'created' => true,
    ];
}
