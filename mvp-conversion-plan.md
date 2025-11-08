# PHPCollab SaaS MVP - What You're Actually Building

**Date:** 2025-11-08
**Key Point:** You're NOT building project management features from scratch - you're converting an existing app to multi-tenant SaaS

---

## What Already Exists (PHPCollab Core)

### ✅ **Already Built - Don't Touch (Save 12+ months of work!)**

**Core Features (90% done):**
```
✓ Project management
  ├─ Projects, phases, tasks, subtasks
  ├─ Task assignment, priorities, due dates
  ├─ Project dashboards
  └─ Gantt charts

✓ Team collaboration
  ├─ Team member management
  ├─ Discussions/forums
  ├─ Notifications
  └─ Activity streams

✓ File management
  ├─ File uploads/downloads
  ├─ Document library
  ├─ File versioning
  └─ Linked content

✓ Calendar
  ├─ Project calendar
  ├─ Task due dates
  └─ Events

✓ Reports
  ├─ Project reports
  ├─ Task reports
  └─ Time tracking reports

✓ Client portal
  ├─ Client-specific views
  ├─ Published content
  └─ Client tasks

✓ Additional features
  ├─ Bookmarks
  ├─ Notes
  ├─ Search
  ├─ Time tracking
  └─ Invoicing (basic)
```

**Technical Stack (Already in place):**
```
✓ PHP 7.4+ codebase
✓ MySQL/PostgreSQL database support
✓ File upload handling
✓ Email notifications (PHPMailer)
✓ PDF generation
✓ Chart generation (jpgraph)
✓ Authentication system
✓ Security (CSRF protection, input validation)
✓ Session management
✓ Multi-language support
```

**This is HUGE** - you're starting with a mature, working application that would take 12-18 months to build from scratch.

---

## What You Need to Build (The SaaS Conversion)

### 🔨 **MVP Work (4-6 months):**

### **1. Multi-Tenant Architecture (Biggest lift - 8-10 weeks)**

**Problem:** PHPCollab currently runs as single-tenant (one installation per company)

**What you need to build:**

```php
// Current PHPCollab (single tenant):
SELECT * FROM tasks WHERE user_id = 123;

// Multi-tenant version you'll build:
SELECT * FROM tasks WHERE tenant_id = 'acme-corp' AND user_id = 123;
```

**Specific work:**

**A. Database Schema Changes (2-3 weeks)**
```sql
-- Add tenant_id to ALL tables
ALTER TABLE organizations ADD COLUMN tenant_id VARCHAR(50);
ALTER TABLE projects ADD COLUMN tenant_id VARCHAR(50);
ALTER TABLE tasks ADD COLUMN tenant_id VARCHAR(50);
ALTER TABLE members ADD COLUMN tenant_id VARCHAR(50);
ALTER TABLE files ADD COLUMN tenant_id VARCHAR(50);
-- ... repeat for ~40 tables

-- Add indexes for performance
CREATE INDEX idx_projects_tenant ON projects(tenant_id);
CREATE INDEX idx_tasks_tenant ON tasks(tenant_id, id);
-- ... repeat for all tenant_id columns

-- Create tenants table
CREATE TABLE tenants (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255),
    subdomain VARCHAR(100) UNIQUE,
    plan VARCHAR(50), -- 'starter', 'professional'
    status VARCHAR(50), -- 'active', 'cancelled', 'suspended'
    stripe_customer_id VARCHAR(100),
    stripe_subscription_id VARCHAR(100),
    storage_used BIGINT DEFAULT 0,
    storage_limit BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**B. Tenant Isolation Layer (3-4 weeks)**
```php
// Create a TenantContext class
class TenantContext {
    private static $currentTenantId = null;

    public static function setTenant($tenantId) {
        self::$currentTenantId = $tenantId;
    }

    public static function getTenant() {
        if (self::$currentTenantId === null) {
            throw new Exception("No tenant context set!");
        }
        return self::$currentTenantId;
    }
}

// Modify base Database class to auto-inject tenant_id
class Database {
    public function query($sql, $params = []) {
        // Automatically add tenant_id to WHERE clauses
        $tenantId = TenantContext::getTenant();

        // Parse SQL and inject tenant_id
        // This is the tricky part - need to be careful!

        return $this->execute($sql, $params);
    }
}

// Update all model classes
class Task {
    public function getById($id) {
        $tenantId = TenantContext::getTenant();

        // Add tenant_id to all queries
        return $this->db->query(
            "SELECT * FROM tasks WHERE tenant_id = ? AND id = ?",
            [$tenantId, $id]
        );
    }
}
```

**C. File Storage Isolation (1-2 weeks)**
```php
// Current: Files stored in /uploads/
// New: Files stored in S3 with tenant prefix

class FileStorage {
    private $s3Client;

    public function store($file, $filename) {
        $tenantId = TenantContext::getTenant();

        // Store in S3: {tenant_id}/files/{filename}
        $key = "{$tenantId}/files/{$filename}";

        return $this->s3Client->putObject([
            'Bucket' => 'phpcollab-files',
            'Key' => $key,
            'Body' => $file,
            'ACL' => 'private'
        ]);
    }

    public function get($filename) {
        $tenantId = TenantContext::getTenant();
        $key = "{$tenantId}/files/{$filename}";

        return $this->s3Client->getObject([
            'Bucket' => 'phpcollab-files',
            'Key' => $key
        ]);
    }
}
```

**D. Tenant Resolution/Routing (1 week)**
```php
// Middleware to determine which tenant based on:
// 1. Subdomain (acme.phpcollab.app)
// 2. Custom domain (projects.acme.com)

class TenantMiddleware {
    public function handle($request) {
        // Extract subdomain or domain
        $host = $request->getHost();

        if (preg_match('/^(.+)\.phpcollab\.app$/', $host, $matches)) {
            $subdomain = $matches[1];
            $tenant = Tenant::findBySubdomain($subdomain);
        } else {
            $tenant = Tenant::findByCustomDomain($host);
        }

        if (!$tenant) {
            return redirect('/signup');
        }

        // Set tenant context for all subsequent queries
        TenantContext::setTenant($tenant->id);

        return $next($request);
    }
}
```

**E. Testing & Migration (1-2 weeks)**
```
- Create data migration scripts
- Test tenant isolation (CRITICAL - can't leak data!)
- Performance testing with multiple tenants
- Security audit (tenant data separation)
```

---

### **2. Billing & Subscriptions (3-4 weeks)**

**What you need to build:**

**A. Stripe Integration (1-2 weeks)**
```php
// Stripe checkout for signup
class SubscriptionController {
    public function createCheckout($plan) {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $plan === 'starter'
                    ? 'price_starter_39'
                    : 'price_professional_69',
                'quantity' => 1,
            ]],
            'success_url' => url('/setup-account?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/pricing'),
        ]);

        return redirect($session->url);
    }

    // Handle successful payment
    public function handleSuccess($sessionId) {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        // Create tenant account
        $tenant = Tenant::create([
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
            'plan' => $session->metadata->plan,
            'status' => 'active'
        ]);

        // Create admin user
        // Redirect to onboarding
    }
}
```

**B. Webhook Handling (1 week)**
```php
// Handle Stripe webhooks for subscription events
class StripeWebhookController {
    public function handle(Request $request) {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // Verify webhook signature
        $event = \Stripe\Webhook::constructEvent(
            $request->getContent(),
            $request->header('Stripe-Signature'),
            env('STRIPE_WEBHOOK_SECRET')
        );

        switch ($event->type) {
            case 'customer.subscription.deleted':
                // Cancel tenant account
                $this->handleCancellation($event->data->object);
                break;

            case 'invoice.payment_failed':
                // Suspend tenant account
                $this->handleFailedPayment($event->data->object);
                break;

            case 'customer.subscription.updated':
                // Handle plan changes
                $this->handlePlanChange($event->data->object);
                break;
        }
    }
}
```

**C. Billing Portal (1 week)**
```php
// Customer portal for managing subscription
class BillingPortalController {
    public function show() {
        $tenant = TenantContext::getCurrentTenant();

        return view('billing', [
            'plan' => $tenant->plan,
            'status' => $tenant->status,
            'next_billing_date' => $tenant->getNextBillingDate(),
            'payment_method' => $tenant->getPaymentMethod(),
        ]);
    }

    public function createPortalSession() {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $tenant = TenantContext::getCurrentTenant();

        $session = $stripe->billingPortal->sessions->create([
            'customer' => $tenant->stripe_customer_id,
            'return_url' => url('/billing'),
        ]);

        return redirect($session->url);
    }
}
```

**D. Plan Switching (Starter ↔ Professional) (3-4 days)**
```php
class PlanController {
    public function upgrade() {
        $tenant = TenantContext::getCurrentTenant();
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // Update subscription
        $subscription = $stripe->subscriptions->retrieve(
            $tenant->stripe_subscription_id
        );

        $stripe->subscriptions->update(
            $tenant->stripe_subscription_id,
            ['items' => [[
                'id' => $subscription->items->data[0]->id,
                'price' => 'price_professional_69',
            ]]]
        );

        // Update tenant record
        $tenant->update(['plan' => 'professional']);
    }
}
```

---

### **3. Self-Service Signup & Onboarding (2-3 weeks)**

**What you need to build:**

**A. Public Signup Flow (1 week)**
```php
// Signup page (new)
Route::get('/signup', 'SignupController@show');
Route::post('/signup', 'SignupController@create');

class SignupController {
    public function show() {
        return view('signup', [
            'plans' => [
                'starter' => ['price' => 39, 'users' => 10],
                'professional' => ['price' => 69, 'users' => 25]
            ]
        ]);
    }

    public function create(Request $request) {
        // Validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:members',
            'company' => 'required',
            'subdomain' => 'required|unique:tenants|alpha_dash',
            'plan' => 'required|in:starter,professional'
        ]);

        // Create Stripe checkout session
        return $this->createCheckout($request);
    }
}
```

**B. Account Setup Wizard (1 week)**
```php
// After successful payment, guide user through setup
class OnboardingController {
    public function step1() {
        // Company details (already have from signup)
        return view('onboarding.step1');
    }

    public function step2() {
        // Invite team members
        return view('onboarding.step2');
    }

    public function step3() {
        // Create first project (optional)
        return view('onboarding.step3');
    }

    public function complete() {
        // Mark onboarding as complete
        $tenant = TenantContext::getCurrentTenant();
        $tenant->update(['onboarded' => true]);

        return redirect('/dashboard');
    }
}
```

**C. Email Verification (2-3 days)**
```php
class EmailVerificationController {
    public function send(User $user) {
        $token = Str::random(64);

        EmailVerification::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => now()->addHours(24)
        ]);

        Mail::to($user)->send(new VerifyEmail($token));
    }

    public function verify($token) {
        $verification = EmailVerification::where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = User::find($verification->user_id);
        $user->update(['email_verified_at' => now()]);

        $verification->delete();

        return redirect('/dashboard');
    }
}
```

---

### **4. Account Management (1-2 weeks)**

**What you need to build:**

**A. Usage Dashboard (3-4 days)**
```php
class UsageController {
    public function show() {
        $tenant = TenantContext::getCurrentTenant();

        return view('usage', [
            'users' => $tenant->getActiveUsers(),
            'user_limit' => $tenant->getUserLimit(),
            'storage' => $tenant->getStorageUsed(),
            'storage_limit' => $tenant->getStorageLimit(),
            'projects' => $tenant->getProjectCount(),
            'tasks' => $tenant->getTaskCount(),
        ]);
    }
}
```

**B. Team Member Management (3-4 days)**
```php
// Already exists in PHPCollab, but need to add:
// - User limits enforcement based on plan
// - Invitation system for new users

class TeamController {
    public function invite(Request $request) {
        $tenant = TenantContext::getCurrentTenant();

        // Check user limit
        if ($tenant->getActiveUsers() >= $tenant->getUserLimit()) {
            return back()->with('error', 'User limit reached. Upgrade to add more users.');
        }

        // Send invitation
        $invitation = Invitation::create([
            'tenant_id' => $tenant->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(64)
        ]);

        Mail::to($request->email)->send(new TeamInvitation($invitation));
    }
}
```

**C. Settings & Preferences (2-3 days)**
```php
// Tenant-level settings
class SettingsController {
    public function show() {
        $tenant = TenantContext::getCurrentTenant();

        return view('settings', [
            'company_name' => $tenant->name,
            'subdomain' => $tenant->subdomain,
            'custom_domain' => $tenant->custom_domain,
            'timezone' => $tenant->timezone,
            'language' => $tenant->language,
        ]);
    }

    public function update(Request $request) {
        $tenant = TenantContext::getCurrentTenant();
        $tenant->update($request->validated());

        return back()->with('success', 'Settings updated');
    }
}
```

**D. Cancellation Flow (1-2 days)**
```php
class CancellationController {
    public function show() {
        return view('cancel', [
            'reasons' => [
                'too_expensive',
                'missing_features',
                'not_using',
                'switching_to_competitor',
                'other'
            ]
        ]);
    }

    public function cancel(Request $request) {
        $tenant = TenantContext::getCurrentTenant();
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // Record cancellation reason
        $tenant->cancellation_reason = $request->reason;
        $tenant->cancellation_feedback = $request->feedback;
        $tenant->save();

        // Cancel Stripe subscription
        $stripe->subscriptions->cancel($tenant->stripe_subscription_id);

        // Mark tenant as cancelled (keep data for 30 days)
        $tenant->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'delete_at' => now()->addDays(30)
        ]);

        return view('cancelled');
    }
}
```

---

### **5. Infrastructure Setup (1-2 weeks)**

**What you need to build/configure:**

**A. AWS Setup (3-4 days)**
```bash
# RDS MySQL setup
aws rds create-db-instance \
    --db-instance-identifier phpcollab-prod \
    --db-instance-class db.t3.medium \
    --engine mysql \
    --master-username admin \
    --master-user-password [secure-password] \
    --allocated-storage 50

# S3 bucket for file storage
aws s3api create-bucket \
    --bucket phpcollab-files \
    --region us-east-1

# S3 bucket policy (private, access via presigned URLs)
aws s3api put-bucket-policy \
    --bucket phpcollab-files \
    --policy file://bucket-policy.json

# ElastiCache Redis for sessions
aws elasticache create-cache-cluster \
    --cache-cluster-id phpcollab-sessions \
    --cache-node-type cache.t3.micro \
    --engine redis \
    --num-cache-nodes 1
```

**B. CI/CD Pipeline (2-3 days)**
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      - name: Run tests
        run: |
          composer install
          ./vendor/bin/codecept run

      - name: Deploy to EC2
        run: |
          ssh deploy@server "cd /var/www/phpcollab && git pull && composer install --no-dev"
          ssh deploy@server "php artisan migrate --force"
          ssh deploy@server "sudo systemctl reload php-fpm"
```

**C. Monitoring & Logging (2-3 days)**
```php
// Error tracking with Sentry
Sentry\init([
    'dsn' => env('SENTRY_DSN'),
    'environment' => env('APP_ENV'),
]);

// Performance monitoring
CloudWatch::putMetric([
    'Namespace' => 'PHPCollab',
    'MetricData' => [
        [
            'MetricName' => 'ResponseTime',
            'Value' => $responseTime,
            'Unit' => 'Milliseconds',
        ]
    ]
]);

// Application logging
Log::info('User signup', [
    'tenant_id' => $tenant->id,
    'plan' => $plan,
]);
```

**D. Backups (1-2 days)**
```bash
# Automated RDS snapshots (daily)
aws rds create-db-snapshot \
    --db-instance-identifier phpcollab-prod \
    --db-snapshot-identifier phpcollab-$(date +%Y%m%d)

# S3 versioning for file recovery
aws s3api put-bucket-versioning \
    --bucket phpcollab-files \
    --versioning-configuration Status=Enabled

# Backup retention policy (7 days)
```

---

### **6. Marketing Site (2-4 weeks)**

**What you need to build:**

**A. Landing Page (1 week)**
```html
<!-- public/index.html (or landing.blade.php) -->
<header>
  <nav>
    <a href="/">PHPCollab</a>
    <a href="/features">Features</a>
    <a href="/pricing">Pricing</a>
    <a href="/login">Login</a>
    <a href="/signup" class="cta">Start Free Trial</a>
  </nav>
</header>

<section class="hero">
  <h1>Project Management for Boutique Teams</h1>
  <p>Save 60% vs per-user pricing. One simple price for your whole team.</p>

  <div class="pricing-preview">
    <div>
      <h3>Starter</h3>
      <p class="price">$39/month</p>
      <p>Up to 10 users</p>
      <a href="/signup?plan=starter">Get Started</a>
    </div>
    <div>
      <h3>Professional</h3>
      <p class="price">$69/month</p>
      <p>Up to 25 users</p>
      <a href="/signup?plan=professional">Get Started</a>
    </div>
  </div>

  <p class="comparison">
    vs Basecamp ($150/mo) • Asana ($110/mo) • Monday.com ($120/mo)
  </p>
</section>

<section class="features">
  <!-- Feature highlights -->
</section>

<section class="social-proof">
  <!-- Customer logos, testimonials -->
</section>

<section class="cta">
  <h2>Start your free 14-day trial</h2>
  <a href="/signup">Get Started - No credit card required</a>
</section>
```

**B. Pricing Page (2-3 days)**
```
Detailed pricing comparison table
FAQ section
"Why we're different" messaging
```

**C. Features Page (2-3 days)**
```
Feature list with screenshots
Use case examples
Integration mentions
```

**D. Documentation/Help Center (1 week)**
```
Getting started guide
Feature documentation
Video tutorials (optional for MVP)
FAQ
```

---

## What You're NOT Building (Simplify for MVP)

### ❌ **Don't Build These Initially:**

**Complex features you can skip:**
```
❌ Advanced reporting (use basic reports from PHPCollab)
❌ Custom integrations (Zapier, Slack, etc.)
❌ Mobile apps (responsive web is enough)
❌ Advanced permissions (PHPCollab has basic permissions)
❌ Time tracking billing (has basic time tracking)
❌ API for third parties (not needed at first)
❌ White labeling (can add later for Agency tier)
❌ SSO/SAML (boutique customers don't need this)
❌ Custom workflows (use PHPCollab defaults)
❌ Advanced analytics (Google Analytics is enough)
```

**Simplifications for boutique market:**
```
✓ Use PHPCollab's existing UI (don't redesign)
✓ Use default permissions model
✓ Use basic email notifications
✓ Use built-in reporting
✓ No complex customization options
```

**This keeps scope manageable** - you're converting, not building from scratch.

---

## MVP Development Timeline

### **Month 1-2: Multi-Tenant Core**
```
Week 1-2: Database schema changes
Week 3-4: Tenant isolation layer
Week 5-6: File storage (S3) + testing
Week 7-8: Security audit + bug fixes

Deliverable: Can run multiple tenants on same installation
```

### **Month 3: Billing + Signup**
```
Week 9-10: Stripe integration + webhooks
Week 11: Self-service signup flow
Week 12: Onboarding wizard

Deliverable: Customers can signup and pay
```

### **Month 4: Account Management + Infrastructure**
```
Week 13: Usage dashboard + settings
Week 14: Team management + invitations
Week 15-16: AWS setup + deployment pipeline

Deliverable: Complete SaaS backend
```

### **Month 5-6: Marketing + Polish**
```
Week 17-18: Landing page + marketing site
Week 19: Documentation + help center
Week 20-21: Beta testing + bug fixes
Week 22-23: SEO content + launch prep
Week 24: LAUNCH 🚀

Deliverable: Public launch with 20-50 beta customers
```

---

## Size of MVP Code You're Writing

**Estimated new code (not counting PHPCollab core):**

```
Multi-tenant layer:           ~3,000 lines
Billing/subscriptions:        ~2,000 lines
Signup/onboarding:           ~1,500 lines
Account management:          ~1,000 lines
Infrastructure config:         ~500 lines
Marketing site:              ~2,000 lines
Tests:                       ~3,000 lines
-------------------------------------------
Total new code:             ~13,000 lines
```

**Compare to building from scratch:** 100,000+ lines

**You're writing 13% the code of a full build** ✅

---

## AI Can Help With All of This

**What AI can generate for you:**

```
✓ Multi-tenant query patterns (80% of boilerplate)
✓ Stripe integration code (90% from docs)
✓ Database migrations (95% automated)
✓ Test cases (85% coverage)
✓ Documentation (70% first draft)
✓ Landing page copy (80% draft)
✓ Email templates (90% complete)
✓ Webhook handlers (85% from Stripe examples)
```

**With AI:** 6 months solo
**Without AI:** 10-12 months solo

---

## Summary: Your MVP Scope

### **What you're building:**
1. ✅ Multi-tenant architecture (biggest work)
2. ✅ Billing/subscriptions (Stripe)
3. ✅ Self-service signup + onboarding
4. ✅ Account management
5. ✅ Infrastructure setup
6. ✅ Marketing website

### **What you're NOT building:**
- ❌ Project management features (already exist!)
- ❌ Complex customization
- ❌ Enterprise features
- ❌ Mobile apps
- ❌ Advanced integrations

### **Timeline:**
- 4-6 months solo with AI help
- ~40-50 hours/week
- Launch with 20-50 beta customers

**This is very achievable because 90% of the product already exists.** You're just making it multi-tenant and adding SaaS billing.

---

Want me to create:
- Detailed multi-tenant architecture guide?
- Step-by-step Stripe integration tutorial?
- Database migration scripts?
- Week-by-week development roadmap?
