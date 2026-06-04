<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class InstallWizardController extends Controller
{
    protected function isInstalled(): bool
    {
        return File::exists(base_path('.installed'));
    }

    public function index()
    {
        if ($this->isInstalled()) {
            return redirect('/login');
        }
        return view('install.step1-check', ['checks' => $this->systemChecks()]);
    }

    public function step1()
    {
        $checks = $this->systemChecks();
        return view('install.step1-check', compact('checks'));
    }

    public function step2()
    {
        return view('install.step2-database');
    }

    public function testDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
        ]);

        try {
            Config::set('database.connections.install', [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => $request->db_database,
                'username' => $request->db_username,
                'password' => $request->db_password ?? '',
            ]);

            DB::connection('install')->getPdo();
            return response()->json(['success' => true, 'message' => 'Koneksi database berhasil!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function step3()
    {
        return view('install.step3-admin');
    }

    public function step4()
    {
        return view('install.step4-bumdes');
    }

    public function step5()
    {
        return view('install.step5-finish');
    }

    public function processStep2(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
        ]);

        session([
            'install.db_host' => $request->db_host,
            'install.db_port' => $request->db_port,
            'install.db_database' => $request->db_database,
            'install.db_username' => $request->db_username,
            'install.db_password' => $request->db_password ?? '',
        ]);

        return redirect()->route('install.step3');
    }

    public function processStep3(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        session([
            'install.admin_name' => $request->admin_name,
            'install.admin_email' => $request->admin_email,
            'install.admin_password' => $request->admin_password,
        ]);

        return redirect()->route('install.step4');
    }

    public function processStep4(Request $request)
    {
        $request->validate([
            'bumdes_name' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            'bumdes_address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        session([
            'install.bumdes_name' => $request->bumdes_name,
            'install.village_name' => $request->village_name,
            'install.bumdes_address' => $request->bumdes_address,
            'install.phone' => $request->phone,
            'install.email' => $request->email ?? '',
        ]);

        return redirect()->route('install.step5');
    }

    public function processInstall()
    {
        try {
            // 1. Write .env
            $this->writeEnv();

            // 2. Clear config cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            // 3. Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // 4. Run seeders
            Artisan::call('db:seed', ['--force' => true]);

            // 5. Create admin user
            $this->createAdmin();

            // 6. Create BUMDes settings
            $this->createBumdesSettings();

            // 7. Create storage link
            Artisan::call('storage:link');

            // 8. Create .installed file
            File::put(base_path('.installed'), now()->toDateTimeString());

            // 9. Clear cache again
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            session()->forget('install');
            return response()->json(['success' => true, 'message' => 'Instalasi berhasil!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    protected function writeEnv()
    {
        $envContent = <<<ENV
APP_NAME="BUMDes Management System"
APP_ENV=local
APP_KEY=base64:{$this->generateKey()}
APP_DEBUG=true
APP_URL=http://{$_SERVER['HTTP_HOST']}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={session('install.db_host')}
DB_PORT={session('install.db_port')}
DB_DATABASE={session('install.db_database')}
DB_USERNAME={session('install.db_username')}
DB_PASSWORD={session('install.db_password')}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

SESSION_SECURE_COOKIE=false
ENV;

        File::put(base_path('.env'), $envContent);
    }

    protected function generateKey(): string
    {
        return base64_encode(random_bytes(32));
    }

    protected function createAdmin()
    {
        DB::table('users')->insert([
            'name' => session('install.admin_name'),
            'email' => session('install.admin_email'),
            'password' => Hash::make(session('install.admin_password')),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createBumdesSettings()
    {
        DB::table('bumdes_settings')->insert([
            'bumdes_name' => session('install.bumdes_name'),
            'village_name' => session('install.village_name'),
            'bumdes_address' => session('install.bumdes_address'),
            'phone' => session('install.phone'),
            'email' => session('install.email'),
            'primary_color' => '#1E3A5F',
            'secondary_color' => '#D4A843',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function systemChecks(): array
    {
        $checks = [];

        // PHP Version
        $checks[] = [
            'name' => 'PHP Version >= 8.1',
            'required' => true,
            'passed' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'current' => PHP_VERSION,
        ];

        // Extensions
        $requiredExtensions = [
            'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml',
            'ctype', 'json', 'bcmath', 'fileinfo', 'gd',
            'mysql', 'curl', 'zip',
        ];

        foreach ($requiredExtensions as $ext) {
            $checks[] = [
                'name' => "Extension: $ext",
                'required' => true,
                'passed' => extension_loaded($ext),
                'current' => extension_loaded($ext) ? 'Installed' : 'Not Found',
            ];
        }

        // Writable directories
        $writableDirs = [
            'storage/app',
            'storage/framework',
            'storage/logs',
            'bootstrap/cache',
        ];

        foreach ($writableDirs as $dir) {
            $path = base_path($dir);
            $checks[] = [
                'name' => "Writable: $dir",
                'required' => true,
                'passed' => is_writable($path),
                'current' => is_writable($path) ? 'Writable' : 'Not Writable',
            ];
        }

        return $checks;
    }
}
