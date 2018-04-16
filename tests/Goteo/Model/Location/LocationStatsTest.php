<?php


namespace Goteo\Model\Location\Tests;

use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Location\LocationStats;
use Goteo\Model\User\UserLocation;
use Goteo\Model\Project\ProjectLocation;

class LocationStatsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

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

        $this->assertInternalType('integer', $unlocated);
        $this->assertInternalType('integer', $located);
        $this->assertEquals(User::dbCount(), $located + $unlocated);
        $this->assertInternalType('integer', $unlocable);
        $this->assertInternalType('integer', $notSpain);
        $this->assertEquals($located, $spain + $notSpain);

        echo "\nUSERS, located: [$located] unlocated: [$unlocated] unlocable: [$unlocable] spain: [$spain] notSpain: [$notSpain]\n";

        $spainRegions = $stats->countGroupFiltered('region', 'country_code', 'ES');
        $this->assertInternalType('array', $spainRegions);
        $this->assertEquals(array_sum($spainRegions), $spain);

        $countries = $stats->countGroupCountries();
        $this->assertInternalType('array', $countries);
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

        $this->assertInternalType('integer', $unlocated);
        $this->assertInternalType('integer', $located);
        $this->assertEquals(Project::dbCount(), $located + $unlocated);
        $this->assertInternalType('integer', $unlocable);
        $this->assertInternalType('integer', $notSpain);
        $this->assertEquals($located, $spain + $notSpain);

        echo "\nPROJECTS, located: [$located] unlocated: [$unlocated] unlocable: [$unlocable] spain: [$spain] notSpain: [$notSpain]\n";

        $spainRegions = $stats->countGroupFiltered('region', 'country_code', 'ES');
        $this->assertInternalType('array', $spainRegions);
        $this->assertEquals(array_sum($spainRegions), $spain);

        $countries = $stats->countGroupCountries();
        $this->assertInternalType('array', $countries);
        $this->assertEquals(array_sum($countries), $located);
    }
}
