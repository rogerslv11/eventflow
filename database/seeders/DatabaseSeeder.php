<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\CheckIn;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\EventStaff;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\Participant;
use App\Models\Schedule;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\StaffRole;
use App\Models\Ticket;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed do EventFlow...');

        DB::transaction(function () {
            $this->seedOrganizations();
            $this->seedUsers();
            $this->seedCategories();
            $this->seedEvents();
            $this->seedTicketTypes();
            $this->seedTicketBatches();
            $this->seedOrdersAndTickets();
            $this->seedParticipants();
            $this->seedCheckIns();
            $this->seedCoupons();
            $this->seedSpeakers();
            $this->seedSchedules();
            $this->seedVendors();
            $this->seedSponsors();
            $this->seedStaff();
            $this->seedActivityLogs();
        });

        $this->command->info('✅ Seed concluído com sucesso!');
        $this->command->info('');
        $this->command->info('👤 Usuários demo:');
        $this->command->info('   Email: demo@eventflow.app  | Senha: password');
        $this->command->info('   Email: admin@eventflow.app | Senha: password');
    }

    protected function seedOrganizations(): void
    {
        $this->command->info('🏢 Criando organizações...');

        $orgs = collect([
            [
                'name' => 'EventFlow Demo',
                'slug' => 'eventflow-demo',
                'email' => 'demo@eventflow.app',
                'phone' => '+55 11 99999-9999',
                'document' => '12.345.678/0001-90',
                'address' => 'Av. Paulista, 1000 - São Paulo, SP',
                'primary_color' => '#7C3AED',
            ],
            [
                'name' => 'TechSummit Produções',
                'slug' => 'techsummit-producoes',
                'email' => 'contato@techsummit.com.br',
                'phone' => '+55 11 98888-7777',
                'document' => '98.765.432/0001-10',
                'address' => 'Rua das Flores, 500 - Rio de Janeiro, RJ',
                'primary_color' => '#3B82F6',
            ],
        ])->map(fn ($data) => Organization::create($data));

        Organization::factory(2)->create()->merge($orgs);
    }

    protected function seedUsers(): void
    {
        $this->command->info('👥 Criando usuários...');

        $orgs = Organization::all();

        // Demo users
        $demoUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@eventflow.app',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin EventFlow',
            'email' => 'admin@eventflow.app',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        foreach ($orgs as $org) {
            $demoUser->organizations()->attach($org->id, ['role' => 'owner', 'joined_at' => now()]);
            $adminUser->organizations()->attach($org->id, ['role' => 'admin', 'joined_at' => now()]);
        }

        // Create more users for the demo organization
        $demoOrg = $orgs->first();
        $users = User::factory(8)->create();

        $roles = ['owner', 'admin', 'manager', 'staff', 'check_in_staff', 'finance', 'viewer', 'admin'];
        foreach ($users as $i => $user) {
            $user->organizations()->attach($demoOrg->id, [
                'role' => $roles[$i] ?? 'staff',
                'joined_at' => now()->subDays(rand(1, 90)),
            ]);
        }
    }

    protected function seedCategories(): void
    {
        $this->command->info('📂 Criando categorias...');

        $categories = [
            ['name' => 'Tecnologia', 'color' => '#7C3AED', 'icon' => 'cpu'],
            ['name' => 'Marketing', 'color' => '#EC4899', 'icon' => 'megaphone'],
            ['name' => 'Negócios', 'color' => '#3B82F6', 'icon' => 'briefcase'],
            ['name' => 'Educação', 'color' => '#10B981', 'icon' => 'book'],
            ['name' => 'Música', 'color' => '#F59E0B', 'icon' => 'music'],
            ['name' => 'Esportes', 'color' => '#EF4444', 'icon' => 'trophy'],
            ['name' => 'Gastronomia', 'color' => '#F97316', 'icon' => 'utensils'],
            ['name' => 'Saúde', 'color' => '#06B6D4', 'icon' => 'heart'],
        ];

        $demoOrg = Organization::where('slug', 'eventflow-demo')->first();

        foreach ($categories as $cat) {
            Category::create([
                'organization_id' => $demoOrg->id,
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'color' => $cat['color'],
                'icon' => $cat['icon'],
                'description' => "Eventos da área de {$cat['name']}",
                'is_active' => true,
            ]);
        }
    }

    protected function seedEvents(): void
    {
        $this->command->info('🎉 Criando eventos...');

        $demoOrg = Organization::where('slug', 'eventflow-demo')->first();
        $techOrg = Organization::where('slug', 'techsummit-producoes')->first();
        $categories = Category::all();
        $users = User::all();

        $eventTemplates = [
            [
                'name' => 'Summit de Tecnologia 2026',
                'description' => 'O maior evento de tecnologia do Brasil reunindo os maiores especialistas do mercado.',
                'status' => EventStatus::Published,
                'is_featured' => true,
                'city' => 'São Paulo',
                'state' => 'SP',
                'capacity' => 2500,
            ],
            [
                'name' => 'Congresso de Marketing Digital',
                'description' => 'Aprenda as estratégias mais avançadas de marketing digital com cases reais.',
                'status' => EventStatus::Published,
                'is_featured' => true,
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'capacity' => 1500,
            ],
            [
                'name' => 'Workshop de Liderança',
                'description' => 'Workshop intensivo para desenvolvimento de líderes de alta performance.',
                'status' => EventStatus::Active,
                'is_featured' => false,
                'city' => 'Belo Horizonte',
                'state' => 'MG',
                'capacity' => 200,
            ],
            [
                'name' => 'Conferência de IA',
                'description' => 'Tudo sobre Inteligência Artificial e o futuro da tecnologia.',
                'status' => EventStatus::Published,
                'is_featured' => true,
                'city' => 'Curitiba',
                'state' => 'PR',
                'capacity' => 1000,
            ],
            [
                'name' => 'Hackathon de Inovação',
                'description' => '48 horas de pura inovação, programação e criatividade.',
                'status' => EventStatus::Published,
                'is_featured' => false,
                'city' => 'Porto Alegre',
                'state' => 'RS',
                'capacity' => 500,
            ],
            [
                'name' => 'Expo de Negócios',
                'description' => 'A maior feira empresarial do país.',
                'status' => EventStatus::Finished,
                'is_featured' => false,
                'city' => 'São Paulo',
                'state' => 'SP',
                'capacity' => 5000,
            ],
            [
                'name' => 'Meetup de Desenvolvedores',
                'description' => 'Encontro mensal da comunidade de desenvolvedores.',
                'status' => EventStatus::Published,
                'is_featured' => false,
                'city' => 'Florianópolis',
                'state' => 'SC',
                'capacity' => 300,
            ],
            [
                'name' => 'Feira de Design',
                'description' => 'Os melhores designers e projetos do Brasil.',
                'status' => EventStatus::Draft,
                'is_featured' => false,
                'city' => 'Brasília',
                'state' => 'DF',
                'capacity' => 800,
            ],
            [
                'name' => 'Festival de Música Indie',
                'description' => 'O melhor da música independente nacional.',
                'status' => EventStatus::Published,
                'is_featured' => false,
                'city' => 'Salvador',
                'state' => 'BA',
                'capacity' => 3000,
            ],
            [
                'name' => 'Encontro de Empreendedores',
                'description' => 'Networking de alto nível para empreendedores.',
                'status' => EventStatus::Cancelled,
                'is_featured' => false,
                'city' => 'Recife',
                'state' => 'PE',
                'capacity' => 600,
            ],
        ];

        foreach ($eventTemplates as $i => $template) {
            $startsAt = match ($template['status']) {
                EventStatus::Active => now()->subHours(2),
                EventStatus::Finished => now()->subDays(30),
                EventStatus::Draft => now()->addDays(60),
                EventStatus::Cancelled => now()->addDays(15),
                default => now()->addDays(rand(5, 90)),
            };

            $endsAt = (clone $startsAt)->modify('+8 hours');

            Event::create([
                'organization_id' => $i % 2 === 0 ? $demoOrg->id : $techOrg->id,
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'name' => $template['name'],
                'slug' => Str::slug($template['name']).'-'.Str::random(4),
                'description' => $template['description'],
                'content' => fake()->paragraphs(4, true),
                'venue_name' => fake()->randomElement(['Centro de Convenções', 'Auditório Principal', 'Espaço Innovation', 'Pavilhão de Eventos']),
                'address' => fake()->streetAddress(),
                'city' => $template['city'],
                'state' => $template['state'],
                'zipcode' => fake()->numerify('#####-###'),
                'latitude' => fake()->latitude(-30, -5),
                'longitude' => fake()->longitude(-60, -35),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'timezone' => 'America/Sao_Paulo',
                'capacity' => $template['capacity'],
                'status' => $template['status'],
                'is_featured' => $template['is_featured'],
                'is_public' => true,
                'published_at' => $template['status'] !== EventStatus::Draft ? now()->subDays(rand(5, 60)) : null,
            ]);
        }
    }

    protected function seedTicketTypes(): void
    {
        $this->command->info('🎫 Criando tipos de ingresso...');

        $events = Event::all();

        foreach ($events as $event) {
            $ticketTypes = [
                ['name' => 'Standard', 'type' => 'paid', 'price' => fake()->randomFloat(2, 50, 150)],
                ['name' => 'VIP', 'type' => 'vip', 'price' => fake()->randomFloat(2, 300, 600)],
                ['name' => 'Premium', 'type' => 'premium', 'price' => fake()->randomFloat(2, 800, 1500)],
                ['name' => 'Cortesia', 'type' => 'courtesy', 'price' => 0],
            ];

            foreach ($ticketTypes as $i => $tt) {
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => $tt['name'],
                    'slug' => Str::slug($tt['name']).'-'.Str::random(3),
                    'description' => "Ingresso {$tt['name']} para o evento {$event->name}",
                    'type' => $tt['type'],
                    'price' => $tt['price'],
                    'quantity' => fake()->numberBetween(50, $event->capacity / 4),
                    'sold' => fake()->numberBetween(0, 30),
                    'min_per_order' => 1,
                    'max_per_order' => 10,
                    'sales_start_at' => now()->subDays(30),
                    'sales_end_at' => $event->starts_at,
                    'is_active' => $event->status !== EventStatus::Cancelled,
                    'sort_order' => $i,
                    'benefits' => $tt['type'] === 'vip' ? ['Acesso VIP', 'Welcome drink', 'Brindes'] : null,
                ]);
            }
        }
    }

    protected function seedTicketBatches(): void
    {
        $this->command->info('📦 Criando lotes...');

        $ticketTypes = TicketType::all();

        foreach ($ticketTypes->take(20) as $tt) {
            foreach (['1º Lote', '2º Lote', 'Lote Final'] as $i => $batchName) {
                TicketBatch::create([
                    'ticket_type_id' => $tt->id,
                    'event_id' => $tt->event_id,
                    'name' => $batchName,
                    'price' => (float) $tt->price + ($i * 50),
                    'quantity' => fake()->numberBetween(20, 100),
                    'sold' => $i === 0 ? fake()->numberBetween(0, 20) : 0,
                    'starts_at' => now()->subDays(30 - ($i * 10)),
                    'ends_at' => now()->addDays(30 + ($i * 10)),
                    'is_active' => true,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    protected function seedOrdersAndTickets(): void
    {
        $this->command->info('🛒 Criando pedidos e ingressos...');

        $events = Event::whereIn('status', [
            EventStatus::Published, EventStatus::Active, EventStatus::Finished,
        ])->get();
        $ticketTypes = TicketType::all();

        foreach ($events as $event) {
            $orderCount = match ($event->status) {
                EventStatus::Finished => 80,
                EventStatus::Active => 60,
                default => fake()->numberBetween(15, 40),
            };

            $eventTicketTypes = $ticketTypes->where('event_id', $event->id);

            for ($i = 0; $i < $orderCount; $i++) {
                $tt = $eventTicketTypes->random();
                $quantity = fake()->numberBetween(1, 3);
                $unitPrice = (float) $tt->price;
                $subtotal = $unitPrice * $quantity;

                $order = Order::create([
                    'code' => 'ORD-'.strtoupper(Str::random(10)),
                    'event_id' => $event->id,
                    'organization_id' => $event->organization_id,
                    'customer_name' => fake()->name(),
                    'customer_email' => fake()->safeEmail(),
                    'customer_document' => fake()->numerify('###.###.###-##'),
                    'customer_phone' => fake()->numerify('+55 ## 9####-####'),
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'total' => $subtotal,
                    'fee' => fake()->randomFloat(2, 2, 8),
                    'status' => OrderStatus::Paid,
                    'payment_method' => fake()->randomElement(['credit_card', 'pix', 'boleto']),
                    'paid_at' => fake()->dateTimeBetween($event->published_at ?? '-30 days', 'now'),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $tt->id,
                    'name' => $tt->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'total' => $subtotal,
                ]);

                $tt->increment('sold', $quantity);

                for ($j = 0; $j < $quantity; $j++) {
                    Ticket::create([
                        'code' => 'TKT-'.strtoupper(Str::random(12)),
                        'event_id' => $event->id,
                        'order_id' => $order->id,
                        'ticket_type_id' => $tt->id,
                        'participant_name' => fake()->name(),
                        'participant_email' => fake()->safeEmail(),
                        'participant_document' => fake()->numerify('###.###.###-##'),
                        'participant_phone' => fake()->numerify('+55 ## 9####-####'),
                        'participant_company' => fake()->optional(0.6)->company(),
                        'participant_role' => fake()->optional(0.6)->jobTitle(),
                        'price_paid' => $unitPrice,
                        'status' => TicketStatus::Active,
                        'qr_code' => Str::uuid()->toString(),
                    ]);
                }

                $event->increment('tickets_sold', $quantity);
            }
        }
    }

    protected function seedParticipants(): void
    {
        $this->command->info('👤 Criando participantes...');

        $events = Event::all();

        foreach ($events as $event) {
            $count = match ($event->status) {
                EventStatus::Finished => 50,
                EventStatus::Active => 30,
                default => fake()->numberBetween(10, 20),
            };

            Participant::factory($count)->create([
                'event_id' => $event->id,
            ]);
        }
    }

    protected function seedCheckIns(): void
    {
        $this->command->info('✅ Criando check-ins...');

        $tickets = Ticket::inRandomOrder()->limit(100)->get();

        foreach ($tickets as $ticket) {
            $event = $ticket->event;

            if ($event->status === EventStatus::Finished || $event->status === EventStatus::Active || fake()->boolean(30)) {
                $eventDate = $event->starts_at;

                if ($eventDate->isFuture()) {
                    continue;
                }

                CheckIn::create([
                    'event_id' => $event->id,
                    'ticket_id' => $ticket->id,
                    'status' => 'success',
                    'gate' => fake()->randomElement(['Portão A', 'Portão B', 'Portão C']),
                    'location' => fake()->randomElement(['Entrada Principal', 'Lateral']),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'checked_in_at' => $eventDate->addMinutes(rand(0, 60)),
                ]);

                $event->increment('check_ins_count');
            }
        }
    }

    protected function seedCoupons(): void
    {
        $this->command->info('🎟️ Criando cupons...');

        $orgs = Organization::all();
        $events = Event::all();

        $codes = [
            ['EVENTFLOW20', 'percentage', 20],
            ['EARLYBIRD15', 'percentage', 15],
            ['PROMO10', 'percentage', 10],
            ['DESCONTO50', 'fixed', 50],
            ['BLACKFRIDAY', 'percentage', 30],
        ];

        foreach ($codes as $codeData) {
            Coupon::create([
                'organization_id' => $orgs->random()->id,
                'event_id' => $events->random()->id,
                'code' => $codeData[0],
                'name' => "Cupom {$codeData[0]}",
                'description' => "Desconto especial com o cupom {$codeData[0]}",
                'type' => $codeData[1],
                'value' => $codeData[2],
                'usage_limit' => fake()->numberBetween(50, 500),
                'usage_count' => fake()->numberBetween(0, 30),
                'usage_limit_per_user' => 1,
                'min_purchase' => 50,
                'max_discount' => $codeData[1] === 'percentage' ? 200 : null,
                'starts_at' => now()->subDays(15),
                'ends_at' => now()->addDays(45),
                'is_active' => true,
            ]);
        }

        Coupon::factory(25)->create();
    }

    protected function seedSpeakers(): void
    {
        $this->command->info('🎤 Criando palestrantes...');

        Speaker::factory(20)->create([
            'organization_id' => Organization::where('slug', 'eventflow-demo')->first()->id,
        ]);
    }

    protected function seedSchedules(): void
    {
        $this->command->info('📅 Criando programações...');

        $events = Event::all();
        $speakers = Speaker::all();

        foreach ($events as $event) {
            if ($event->status === EventStatus::Draft) {
                continue;
            }

            $eventDate = $event->starts_at;

            for ($i = 0; $i < 6; $i++) {
                $startsAt = (clone $eventDate)->modify("+{$i} hours");
                $endsAt = (clone $startsAt)->modify('+45 minutes');

                Schedule::create([
                    'event_id' => $event->id,
                    'speaker_id' => $speakers->random()->id,
                    'title' => fake()->randomElement([
                        'Abertura do Evento',
                        'Palestra Magna',
                        'Painel de Líderes',
                        'Workshop Prático',
                        'Networking & Coffee',
                        'Encerramento',
                    ]).' - '.fake()->word(),
                    'description' => fake()->paragraph(),
                    'type' => fake()->randomElement(['talk', 'workshop', 'panel', 'break', 'opening', 'closing']),
                    'room' => fake()->randomElement(['Auditório Principal', 'Sala A', 'Sala B']),
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'is_featured' => fake()->boolean(20),
                ]);
            }
        }
    }

    protected function seedVendors(): void
    {
        $this->command->info('🚚 Criando fornecedores...');

        Vendor::factory(20)->create([
            'organization_id' => Organization::where('slug', 'eventflow-demo')->first()->id,
        ]);
    }

    protected function seedSponsors(): void
    {
        $this->command->info('🏆 Criando patrocinadores...');

        $events = Event::where('is_featured', true)->get();

        foreach ($events as $event) {
            $sponsors = [
                ['Master', fake()->company()],
                ['Gold', fake()->company()],
                ['Gold', fake()->company()],
                ['Silver', fake()->company()],
                ['Silver', fake()->company()],
                ['Bronze', fake()->company()],
                ['Bronze', fake()->company()],
                ['Support', fake()->company()],
            ];

            foreach ($sponsors as $i => $sponsor) {
                Sponsor::create([
                    'event_id' => $event->id,
                    'organization_id' => $event->organization_id,
                    'name' => $sponsor[1],
                    'website' => fake()->url(),
                    'description' => fake()->sentence(),
                    'tier' => strtolower($sponsor[0]),
                    'contribution' => match ($sponsor[0]) {
                        'Master' => 100000,
                        'Gold' => 50000,
                        'Silver' => 25000,
                        'Bronze' => 10000,
                        default => 5000,
                    },
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        Sponsor::factory(7)->create();
    }

    protected function seedStaff(): void
    {
        $this->command->info('👨‍💼 Criando equipe...');

        $events = Event::whereIn('status', [EventStatus::Published, EventStatus::Active, EventStatus::Finished])->get();

        foreach ($events as $event) {
            // Create staff roles
            $roles = [
                ['name' => 'Coordenador', 'color' => '#7C3AED'],
                ['name' => 'Recepção', 'color' => '#10B981'],
                ['name' => 'Check-in', 'color' => '#F59E0B'],
                ['name' => 'Segurança', 'color' => '#EF4444'],
                ['name' => 'Produção', 'color' => '#3B82F6'],
            ];

            foreach ($roles as $role) {
                StaffRole::create([
                    'event_id' => $event->id,
                    'name' => $role['name'],
                    'slug' => Str::slug($role['name']),
                    'color' => $role['color'],
                    'permissions' => ['view_event', 'check_in'],
                ]);
            }

            // Create staff members
            for ($i = 0; $i < 8; $i++) {
                EventStaff::create([
                    'event_id' => $event->id,
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'phone' => fake()->numerify('+55 ## 9####-####'),
                    'document' => fake()->numerify('###.###.###-##'),
                    'type' => fake()->randomElement(['employee', 'freelancer', 'volunteer']),
                    'status' => 'active',
                ]);
            }
        }
    }

    protected function seedActivityLogs(): void
    {
        $this->command->info('📜 Criando logs de atividade...');

        $users = User::all();
        $events = Event::all();

        $actions = ['created', 'updated', 'login', 'logout', 'check_in', 'purchase', 'exported'];

        for ($i = 0; $i < 200; $i++) {
            ActivityLog::create([
                'organization_id' => Organization::inRandomOrder()->first()->id,
                'user_id' => $users->random()->id,
                'action' => fake()->randomElement($actions),
                'entity_type' => fake()->randomElement([
                    'App\\Models\\Event',
                    'App\\Models\\Ticket',
                    'App\\Models\\Order',
                    'App\\Models\\User',
                ]),
                'entity_id' => fake()->numberBetween(1, 100),
                'description' => fake()->sentence(),
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'performed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);
        }
    }
}
