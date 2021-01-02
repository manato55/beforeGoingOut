<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RegistersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RegistersTable Test Case
 */
class RegistersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RegistersTable
     */
    public $Registers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Registers',
        'app.Checks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Registers') ? [] : ['className' => RegistersTable::class];
        $this->Registers = TableRegistry::getTableLocator()->get('Registers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Registers);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
