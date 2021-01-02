<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GroupingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GroupingsTable Test Case
 */
class GroupingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GroupingsTable
     */
    public $Groupings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Groupings',
        'app.Members',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Groupings') ? [] : ['className' => GroupingsTable::class];
        $this->Groupings = TableRegistry::getTableLocator()->get('Groupings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Groupings);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
