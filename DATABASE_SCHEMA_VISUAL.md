# 📊 ELITE FOREX PRO - DATABASE SCHEMA OVERVIEW

## 🗂️ **COMPLETE DATABASE STRUCTURE**

### **📋 TABLE RELATIONSHIPS DIAGRAM**

```
┌─────────────────────────────────────────────────────────────────┐
│                    ELITE FOREX PRO DATABASE                    │
│                    (Laravel + MySQL)                           │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐    ┌─────────────────┐    ┌──────────────────┐
│    USERS     │    │   TRANSACTIONS  │    │    DEPOSITS      │
│──────────────│    │─────────────────│    │──────────────────│
│ id (PK)      │◄──►│ id (PK)         │    │ id (PK)          │
│ name         │    │ user_id (FK)    │    │ user_id (FK)     │
│ email        │    │ type            │    │ amount           │
│ password     │    │ amount          │    │ currency         │
│ wallet_balance│    │ description     │    │ status           │
│ language_pref│    │ status          │    │ transaction_id   │
│ is_admin     │    │ reference       │    │ wallet_address   │
│ is_active    │    │ created_at      │    │ screenshot       │
│ created_at   │    │ updated_at      │    │ admin_notes      │
│ updated_at   │    │ transaction_id  │    │ processed_by     │
└──────────────┘    │ canceled_at     │    │ processed_at     │
                    │ canceled_by     │    │ created_at       │
                    │ cancel_reason   │    │ updated_at       │
                    └─────────────────┘    └──────────────────┘
                             │
                             ▼
┌──────────────────┐    ┌─────────────────┐    ┌──────────────────┐
│   WITHDRAWALS    │    │     TRADES      │    │  USER_WALLETS    │
│──────────────────│    │─────────────────│    │──────────────────│
│ id (PK)          │    │ id (PK)         │    │ id (PK)          │
│ user_id (FK)     │    │ user_id (FK)    │    │ user_id (FK)     │
│ amount           │    │ symbol          │    │ currency         │
│ withdrawal_addr  │    │ side            │    │ balance          │
│ status           │    │ quantity        │    │ available_bal    │
│ reference        │    │ price           │    │ locked_balance   │
│ admin_notes      │    │ total_value     │    │ created_at       │
│ processed_by     │    │ profit_loss     │    │ updated_at       │
│ processed_at     │    │ status          │    └──────────────────┘
│ transaction_hash │    │ executed_at     │
│ fee              │    │ created_at      │
│ crypto_symbol    │    │ updated_at      │
│ created_at       │    └─────────────────┘
│ updated_at       │
└──────────────────┘

┌──────────────────┐    ┌─────────────────┐    ┌──────────────────┐
│  CRYPTO_WALLETS  │    │ CHAT_MESSAGES   │    │  SYSTEM_LOGS     │
│──────────────────│    │─────────────────│    │──────────────────│
│ id (PK)          │    │ id (PK)         │    │ id (PK)          │
│ currency         │    │ user_id (FK)    │    │ level            │
│ currency_name    │    │ message         │    │ message          │
│ wallet_address   │    │ is_admin        │    │ context          │
│ network          │    │ created_at      │    │ user_id          │
│ is_active        │    │ updated_at      │    │ ip_address       │
│ qr_code_path     │    └─────────────────┘    │ user_agent       │
│ notes            │                           │ created_at       │
│ created_at       │                           │ updated_at       │
│ updated_at       │                           └──────────────────┘
└──────────────────┘

┌──────────────────┐    ┌─────────────────┐
│ SYSTEM_SETTINGS  │    │  BANK_DETAILS   │
│──────────────────│    │─────────────────│
│ id (PK)          │    │ id (PK)         │
│ key              │    │ user_id (FK)    │
│ value            │    │ bank_name       │
│ description      │    │ account_name    │
│ created_at       │    │ account_number  │
│ updated_at       │    │ routing_number  │
└──────────────────┘    │ swift_code      │
                        │ created_at      │
                        │ updated_at      │
                        └─────────────────┘
```

---

## 🔍 **DETAILED TABLE SPECIFICATIONS**

### **1. USERS TABLE**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    wallet_balance DECIMAL(15,8) DEFAULT 0,
    language_preference VARCHAR(5) DEFAULT 'en',
    is_admin BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    withdrawal_verification_step INT DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### **2. WITHDRAWALS TABLE**
```sql
CREATE TABLE withdrawals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,8) NOT NULL,
    withdrawal_address VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
    reference VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    processed_by BIGINT UNSIGNED NULL,
    processed_at TIMESTAMP NULL,
    transaction_hash VARCHAR(255) NULL,
    fee DECIMAL(15,8) DEFAULT 0,
    crypto_symbol VARCHAR(10) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_status_created (status, created_at)
);
```

### **3. CRYPTO_WALLETS TABLE**
```sql
CREATE TABLE crypto_wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    currency VARCHAR(10) NOT NULL UNIQUE,
    currency_name VARCHAR(50) NOT NULL,
    wallet_address VARCHAR(255) NOT NULL,
    network VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    qr_code_path VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### **4. DEPOSITS TABLE**
```sql
CREATE TABLE deposits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,8) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    transaction_id VARCHAR(255) NULL,
    wallet_address VARCHAR(255) NULL,
    screenshot VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    processed_by BIGINT UNSIGNED NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id)
);
```

### **5. USER_WALLETS TABLE**
```sql
CREATE TABLE user_wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    currency VARCHAR(10) NOT NULL,
    balance DECIMAL(15,8) DEFAULT 0,
    available_balance DECIMAL(15,8) DEFAULT 0,
    locked_balance DECIMAL(15,8) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_currency (user_id, currency)
);
```

---

## 🛠️ **RAILWAY DATABASE CREATION COMMANDS**

### **Method 1: Automatic (Recommended)**
```bash
# This happens automatically when you deploy to Railway
# The start.sh script runs these commands:
php artisan migrate --force --no-interaction
php artisan db:seed --class=AdminUserSeeder --force --no-interaction
```

### **Method 2: Manual via Railway Console**
```bash
# Access Railway Console and run:
cd /app

# Create all tables
php artisan migrate --force

# Seed admin user
php artisan db:seed --class=AdminUserSeeder

# Verify migration status
php artisan migrate:status

# Check tables exist
php artisan tinker
DB::select('SHOW TABLES');
exit
```

### **Method 3: Direct SQL Import**
```sql
-- You can also import the complete schema via MySQL console
-- Use the database_schema.sql file in your project root
```

---

## ✅ **VERIFICATION COMMANDS**

### **Check All Tables Created:**
```bash
# Via Railway Console
php artisan tinker
DB::select('SHOW TABLES');

# Should show these tables:
# - users
# - transactions  
# - deposits
# - withdrawals
# - trades
# - user_wallets
# - crypto_wallets
# - system_logs
# - chat_messages
# - system_settings
# - bank_details
# - migrations
# - password_resets
# - personal_access_tokens
# - failed_jobs
```

### **Check Admin User Created:**
```bash
php artisan tinker
$admin = App\Models\User::where('email', 'admin@eliteforexpro.com')->first();
echo $admin ? 'Admin user exists' : 'Admin user not found';
```

### **Verify Database Connection:**
```bash
php artisan migrate:status
# Should show all migrations as "Ran"
```

---

## 🎯 **EXPECTED RESULT**

After successful database creation, you'll have:

✅ **15 Main Tables** for Elite Forex Pro functionality
✅ **4 Laravel System Tables** for framework features  
✅ **All Foreign Key Relationships** properly established
✅ **Indexes and Constraints** for optimal performance
✅ **Admin User Account** ready for login
✅ **Complete Database Structure** for trading platform

---

**🚀 Your Railway deployment will automatically create this entire database structure!**
