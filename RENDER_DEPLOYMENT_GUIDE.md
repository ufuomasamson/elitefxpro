# 🚀 Elite Forex Pro - Render Deployment Guide

## 📋 Prerequisites

1. **GitHub Repository**: Your code should be pushed to GitHub
2. **Render Account**: Sign up at [render.com](https://render.com)
3. **Database Ready**: MySQL database configuration

## 🛠️ Deployment Steps

### 1. **Connect GitHub Repository**
- Go to [Render Dashboard](https://dashboard.render.com)
- Click "New +" → "Web Service"
- Connect your GitHub repository
- Select the `main` branch

### 2. **Configure Web Service**
```yaml
Name: elite-forex-pro
Environment: PHP
Build Command: composer install --no-dev --optimize-autoloader
Start Command: bash start.sh
```

### 3. **Environment Variables**
Set these environment variables in Render dashboard:

```bash
APP_NAME="Elite Forex Pro"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key-here
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=crypto_broker
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

LOG_CHANNEL=stderr
LOG_LEVEL=info
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Crypto API
COINGECKO_API_KEY=your-coingecko-key
COINGECKO_API_URL=https://api.coingecko.com/api/v3

# Supported languages
SUPPORTED_LANGUAGES=en,it,fr,de,ru
DEFAULT_LANGUAGE=en
```

### 4. **Database Setup**
Create a MySQL database on Render:
- Go to Dashboard → "New +" → "PostgreSQL" (or external MySQL)
- Note the connection details
- Update your environment variables

### 5. **Domain Configuration**
- Custom domain: Configure in Render dashboard
- SSL: Automatically provided by Render
- Update `APP_URL` with your domain

## 🔧 Build Configuration

### **render.yaml** (Optional)
The project includes a `render.yaml` file for Infrastructure as Code:

```yaml
services:
  - type: web
    name: elite-forex-pro
    env: php
    buildCommand: composer install --no-dev --optimize-autoloader
    startCommand: bash start.sh
```

### **Build Process**
1. Composer install dependencies
2. Generate application key
3. Run database migrations
4. Cache configuration, routes, and views
5. Start Laravel server

## 📁 File Structure for Render

```
elite-forex-pro/
├── render.yaml          # Render configuration
├── Procfile            # Process definition
├── start.sh            # Startup script
├── composer.json       # PHP dependencies
├── .env.example        # Environment template
└── app/               # Laravel application
```

## 🚀 Deployment Process

1. **Push to GitHub**: Commit all changes
2. **Create Service**: Connect repo in Render
3. **Set Environment**: Configure all variables
4. **Deploy**: Render automatically builds and deploys
5. **Verify**: Check application health

## 🔍 Troubleshooting

### **Common Issues**

1. **Build Failures**
   ```bash
   # Check logs in Render dashboard
   # Verify composer.json syntax
   # Ensure all dependencies are available
   ```

2. **Database Connection**
   ```bash
   # Verify DB_HOST, DB_USERNAME, DB_PASSWORD
   # Check database accessibility
   # Confirm database name exists
   ```

3. **File Permissions**
   ```bash
   # Render handles permissions automatically
   # start.sh sets proper storage permissions
   ```

4. **Environment Variables**
   ```bash
   # Ensure APP_KEY is generated
   # Verify all required variables are set
   # Check for typos in variable names
   ```

## 📊 Post-Deployment Checklist

- [ ] Application loads successfully
- [ ] Database connection working
- [ ] Admin login functional
- [ ] Trading interface operational
- [ ] Real-time prices updating
- [ ] File uploads working
- [ ] Email notifications (if configured)
- [ ] SSL certificate active
- [ ] Custom domain configured (if applicable)

## 🛡️ Security Considerations

1. **Environment Variables**: Never commit `.env` to repository
2. **Database Security**: Use strong passwords
3. **APP_DEBUG**: Must be `false` in production
4. **APP_KEY**: Generate unique application key
5. **HTTPS**: Render provides SSL automatically

## 📝 Notes

- **Startup Time**: First deploy may take 2-3 minutes
- **Auto-Deploy**: Enabled for `main` branch
- **Health Checks**: Automatic health monitoring
- **Logs**: Available in Render dashboard
- **Scaling**: Can upgrade plans as needed

## 🎯 Success Indicators

✅ **Build Status**: "Live" in Render dashboard  
✅ **Health Check**: Passing  
✅ **Application**: Accessible via URL  
✅ **Database**: Connected and migrated  
✅ **Admin Panel**: Functional  

---

**🚀 Your Elite Forex Pro platform is now live on Render!**

For support and updates, refer to the Render documentation and Laravel deployment best practices.
