<?php
/**
 * Copyright (c) 2013 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace Test\BackgroundJob;


class TestJob extends \OC\BackgroundJob\Job {
	private $testCase;

	/**
	 * @var callable $callback
	 */
	private $callback;

	/**
	 * @param Job $testCase
	 * @param callable $callback
	 */
	public function __construct($testCase, $callback) {
		$this->testCase = $testCase;
		$this->callback = $callback;
	}

	public function run($argument) {
		$this->testCase->markRun();
		$callback = $this->callback;
		$callback($argument);
	}
}

class Job extends \PHPUnit_Framework_TestCase {
	private $run = false;

	public function setUp() {
		$this->run = false;
	}

	public function testRemoveAfterException() {
		$jobList = new DummyJobList();
		$job = new TestJob($this, function () {
			throw new \Exception();
		});
		$jobList->add($job);

		$this->assertCount(1, $jobList->getAll());
		$job->execute($jobList);
		$this->assertTrue($this->run);
		$this->assertCount(0, $jobList->getAll());
	}

	public function markRun() {
		$this->run = true;
	}
}
