<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UniplexTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     * @throws \Throwable
     */
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $json = file_get_contents(resource_path('test_data/login_credentials.json'));
            $login_credentials = json_decode($json, true);

            $browser->visit('/');
            foreach ($login_credentials as $login_credential){
                $browser
                    ->click('#mui-1')
                    ->pause(2000)
                    ->keys('#mui-1', ['{backspace}'])
                    ->pause(2000)
                    ->click('#mui-2')
                    ->keys('#mui-2', ['{backspace}'])
                    ->pause(2000)
                    ->type('email', $login_credential['email'])
                    ->clear('#mui-2')
                    ->pause(2000)
                    ->type('password', $login_credential['password'])
                    ->pause(2000)
                    ->press('Login')
                    ->pause(2000);

                $assertion = $login_credential['email'] === '01730785310' && $login_credential['password'] === 'SWTesting2023'
                                ? 'Welcome' : 'Powerful & Customizable Education ERP';
                $browser->assertSee($assertion)
                    ->pause(2000);

                if($assertion === 'Welcome'){
                    $browser
                        ->click('button.muiltr-19kdti')
                        ->pause(2000)
                        ->click("li.MuiMenuItem-root:nth-child(3) > div:nth-child(2) > span:nth-child(1)")
                        ->pause(2000)
                        ->assertUrlIs('https://uniplex.mist.ac.bd/login');
                }
            }
        });
    }

    public function testFailedLogin(): void
    {
        # test case 2
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(2000)
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'wrong password')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->assertSee('Bad credentials');

        });
    }

    public function testLogout(): void
    {
        $this->browse(function (Browser $browser){
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('button.muiltr-19kdti')
                ->pause(2000)
                ->click("li.MuiMenuItem-root:nth-child(3) > div:nth-child(2) > span:nth-child(1)")
                ->pause(2000)
                ->assertUrlIs('https://uniplex.mist.ac.bd/login');
        });
    }

    public function testChangePassword(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('a.MuiListItem-button[href="/profile"]')
                ->pause(2000)
                ->press('Change Password')
                ->pause(1000)
                ->assertSee('Change Password')
                ->type('oldPassword', 'SWTesting2023')
                ->type('newPassword', 'SWTesting2024')
                ->type('confirmPassword', 'SWTesting2024')
                ->press('Change')
                ->pause(2000)
                ->assertSee('Welcome!')
            ;
        });
    }

    public function testOldPasswordMismatch(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('a.MuiListItem-button[href="/profile"]')
                ->pause(2000)
                ->press('Change Password')
                ->pause(1000)
                ->assertSee('Change Password')
                ->type('oldPassword', 'SWTesting2023322')
                ->type('newPassword', 'SWTesting2024')
                ->type('confirmPassword', 'SWTesting2024')
                ->press('Change')
                ->pause(2000)
                ->assertSee('Old password mismatch');
        });
    }

    public function testNewAndConfirmPasswordMismatch(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('a.MuiListItem-button[href="/profile"]')
                ->pause(2000)
                ->press('Change Password')
                ->pause(1000)
                ->assertSee('Change Password')
                ->type('oldPassword', 'SWTesting2023')
                ->type('newPassword', 'SWTesting2024')
                ->type('confirmPassword', 'SWTesting2025')
                ->press('Change')
                ->pause(2000)
                ->assertSee('New Password and Confirm Password Mismatch');
        });
    }

    public function testSupplementaryEligibilityCheck(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('ul.muiltr-16hrwgb:nth-child(9) > li:nth-child(1)')
                ->pause(2000)
                ->click('ul.open:nth-child(9) > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > a:nth-child(1)')
                ->pause(2000)
                ->assertSee('Course Offering (Supplementary)')
            ;
        });
    }

    /**
     * @throws \Throwable
     */
    public function testMarksEntry(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->clickLink('My Courses')
                ->pause(2000)
                ->assertSee('My Courses')
                ->click('tbody.MuiTableBody-root tr.MuiTableRow-root:nth-child(2)')
                ->pause(2000)
                ->click('tbody.MuiTableBody-root tr.MuiTableRow-root:nth-child(1)')
                ->pause(5000)
            ;

            // Collect all tabs and grab the last one (recently opened).
            $window = collect($browser->driver->getWindowHandles())->last();

            // Switch to the new tab
            $browser->driver->switchTo()->window($window);

            $browser->press('Continuous Assessment')
                ->pause(2000)
                ->assertSee('*Marks entry deadline')
                ->press('Marks Entry')
                ->pause(3000)
                ->keys('#mui-8', ['{backspace}'])
                ->type('#mui-8', -4)
                ->pause(2000)
                ->press('PUBLISH')
                ->pause(2000)
                ->keys('#mui-8', ['{backspace}', '{backspace}'])
                ->type('#mui-8', 32)
                ->pause(2000)
                ->press('PUBLISH')
                ->pause(2000)
                ->assertSee('Failed')
                ->keys('#mui-8', ['{backspace}', '{backspace}'])
                ->type('#mui-8', 2)
                ->pause(2000)
//                ->press('PUBLISH')
//                ->pause(5000)
//                ->assertSee('Marks published successfully')
            ;
        });
    }

    /**
     * @throws \Throwable
     */
    public function testObeConfiguration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->clickLink('My Courses')
                ->pause(2000)
                ->assertSee('My Courses')
                ->click('tbody.MuiTableBody-root tr.MuiTableRow-root:nth-child(2)')
                ->pause(2000)
                ->click('tbody.MuiTableBody-root tr.MuiTableRow-root:nth-child(1)')
                ->pause(5000);

            // Collect all tabs and grab the last one (recently opened).
            $window = collect($browser->driver->getWindowHandles())->last();

            // Switch to the new tab
            $browser->driver->switchTo()->window($window);

            $browser->press('Continuous Assessment')
                ->pause(2000)
                ->assertSee('*Marks entry deadline')
                ->click('.MuiTableBody-root > tr:nth-child(1) > td:nth-child(9) > div:nth-child(1) > button:nth-child(2)')
                ->pause(2000)
                ->click('li.MuiMenuItem-root:nth-child(1)')
                ->pause(2000)
                ->assertSee('OBE Configuration')
                ;

        });
    }

    /**
     * @throws \Throwable
     */
    public function testStudent(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('email', '01730785310')
                ->pause(2000)
                ->type('password', 'SWTesting2023')
                ->pause(2000)
                ->press('Login')
                ->pause(3000)
                ->click('ul.muiltr-16hrwgb:nth-child(14) > li:nth-child(1)')
                ->pause(2000)
                ->click('.MuiCollapse-entered > div:nth-child(1) > div:nth-child(1) > a:nth-child(1)')
                ->pause(3000)
                ->type('filterText','201914052')
                ->pause(2000)
                ->press('Filter')
                ->pause(3000)
                ->assertSee('Zariful Islam')
                ->click('.MuiTableRow-hover')
                ->pause(2000)
                ->press('Results History')
                ->pause(2000)
                ->assertSeeIn('div.muiltr-4cxybv:nth-child(1) > div:nth-child(2) > table:nth-child(1) > tbody:nth-child(2) > tr:nth-child(5) > td:nth-child(4) > div:nth-child(1)', 'F')
            ;
        });
    }
}
