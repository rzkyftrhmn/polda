<?php
namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Repositories\AuthRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\DivisionRepository;
use App\Repositories\PelaporanRepository;
use App\Repositories\PermissionRepository;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\InstitutionRepository;
use App\Repositories\SubDivisionRepository;
use App\Repositories\ReportJourneyRepository;
use App\Repositories\NotificationRepository;
use App\Interfaces\ProfileRepositoryInterface;
use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\PelaporanRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\InstitutionRepositoryInterface;
use App\Interfaces\SubDivisionRepositoryInterface;
use App\Interfaces\ReportJourneyRepositoryInterface;

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
        $this->app->bind(PelaporanRepositoryInterface::class, PelaporanRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(NotificationRepository::class, NotificationRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = config('app.url');

        if (is_string($appUrl) && parse_url($appUrl, PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }
    }
}
