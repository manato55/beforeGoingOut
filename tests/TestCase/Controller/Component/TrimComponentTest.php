<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\TrimComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\TrimComponent Test Case
 */
class TrimComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\TrimComponent
     */
    public $Trim;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Trim = new TrimComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trim);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
