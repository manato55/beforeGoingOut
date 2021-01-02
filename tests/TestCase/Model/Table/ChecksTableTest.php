<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChecksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChecksTable Test Case
 */
class ChecksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChecksTable
     */
    public $Checks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::getTableLocator()->exists('Checks') ? [] : ['className' => ChecksTable::class];
        $this->Checks = TableRegistry::getTableLocator()->get('Checks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Checks);

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
