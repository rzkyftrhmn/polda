<?php
namespace App\Providers;

use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\InstitutionRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use App\Interfaces\ReportFollowUpRepositoryInterface;
use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\SubDivisionRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\DivisionRepository;
use App\Repositories\InstitutionRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\ReportFollowUpRepository;
use App\Repositories\ReportJourneyRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SubDivisionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(InstitutionRepositoryInterface::class, InstitutionRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(SubDivisionRepositoryInterface::class, SubDivisionRepository::class);
        $this->app->bind(ReportJourneyRepositoryInterface::class, ReportJourneyRepository::class);
        $this->app->bind(ReportFollowUpRepositoryInterface::class, ReportFollowUpRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
