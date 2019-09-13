<?php

namespace Illuminated\Testing\Asserts;

use Illuminate\Support\Str;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

trait ScheduleAsserts
{
    protected function seeScheduleCount($count)
    {
        $message = "Failed asserting that schedule events count is {$count}.";
        $this->assertCount($count, app(Schedule::class)->events(), $message);
    }

    protected function dontSeeScheduleCount($count)
    {
        $message = "Failed asserting that schedule events count is not {$count}.";
        $this->assertNotCount($count, app(Schedule::class)->events(), $message);
    }

    protected function seeInSchedule($command, $expression, $runInBackground = false)
    {
        $event = $this->getScheduleEvent($command);

        $message = "Failed asserting that command `{$command}` is in schedule.";
        $this->assertNotEmpty($event, $message);
        $this->assertInstanceOf(Event::class, $event, $message);

        $message = "Failed asserting that command `{$command}` is in schedule as `{$expression}`.";
        $expression = $this->normalizeScheduleExpression(clone $event, $expression);
        $this->assertEquals($expression, $event->expression, $message);

        $message = "Failed asserting that command `{$command}` is scheduled with the same `run in background` mode.";
        $this->assertEquals($runInBackground, $event->runInBackground, $message);
    }

    protected function dontSeeInSchedule($command)
    {
        $message = "Failed asserting that command `{$command}` is not in schedule.";
        $this->assertEmpty($this->getScheduleEvent($command), $message);
    }

    private function getScheduleEvent($command)
    {
        $schedule = app(Schedule::class);

        foreach ($schedule->events() as $event) {
            if (Str::endsWith($event->command, $command)) {
                return $event;
            }
        }

        return false;
    }

    private function normalizeScheduleExpression(Event $event, $expression)
    {
        if (method_exists($event, $expression)) {
            $event->$expression();
            return $event->getExpression();
        }

        return $expression;
    }
}
