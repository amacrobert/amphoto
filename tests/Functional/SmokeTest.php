<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    /** @testdox The main page should load successfully */
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Andrew MacRobert Photography');
    }

    /** @testdox The nightlife portfolio should load successfully */
    public function testNightlifePortfolio(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/photos/nightlife-events');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.page-title h2', 'Nightlife');
    }

    /** @testdox The Bookings page should load successfully */
    public function testInquiries(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/bookings');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.page-title h2', 'Bookings and Rates');
    }
}
