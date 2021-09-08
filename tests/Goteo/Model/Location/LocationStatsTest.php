<?php


namespace Goteo\Model\Location\Tests;

use Goteo\Core\DB;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Location\LocationStats;
use Goteo\Model\User\UserLocation;
use Goteo\Model\Project\ProjectLocation;

class LocationStatsTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        DB::cache(false);

        $stats = new LocationStats(new UserLocation, new User);

        $this->assertInstanceOf('\Goteo\Model\Location\LocationStats', $stats);
    }

    public function testUserStats() {
        $stats = new LocationStats(new UserLocation, new User);
        $this->assertInstanceOf('\Goteo\Model\Location\LocationStats', $stats);
        $unlocated = $stats->countUnlocated();
        $located = $stats->countLocated();
        $unlocable = $stats->countUnlocable();
        $spain = $stats->countFiltered('country_code', 'ES');
        $notSpain = $stats->countFiltered('country_code', 'ES', true);

        $this->assertIsInt($unlocated);
        $this->assertIsInt($located);
        $this->assertEquals(User::dbCount(), $located + $unlocated);
        $this->assertIsInt($unlocable);
        $this->assertIsInt($notSpain);
        $this->assertEquals($located, $spain + $notSpain);

        $spainRegions = $stats->countGroupFiltered('region', 'country_code', 'ES');
        $this->assertIsArray($spainRegions);
        $this->assertEquals(array_sum($spainRegions), $spain);

        $countries = $stats->countGroupCountries();
        $this->assertIsArray($countries);
        $this->assertEquals(array_sum($countries), $located);
    }

    public function testProjectStats() {
        $stats = new LocationStats(new ProjectLocation, new Project);
        $this->assertInstanceOf('\Goteo\Model\Location\LocationStats', $stats);
        $unlocated = $stats->countUnlocated();
        $located = $stats->countLocated();
        $unlocable = $stats->countUnlocable();
        $spain = $stats->countFiltered('country_code', 'ES');
        $notSpain = $stats->countFiltered('country_code', 'ES', true);

        $this->assertIsInt($unlocated);
        $this->assertIsInt($located);
        $this->assertEquals(Project::dbCount(), $located + $unlocated);
        $this->assertIsInt($unlocable);
        $this->assertIsInt($notSpain);
        $this->assertEquals($located, $spain + $notSpain);

        $spainRegions = $stats->countGroupFiltered('region', 'country_code', 'ES');
        $this->assertIsArray($spainRegions);
        $this->assertEquals(array_sum($spainRegions), $spain);

        $countries = $stats->countGroupCountries();
        $this->assertIsArray($countries);
        $this->assertEquals(array_sum($countries), $located);
    }
}
