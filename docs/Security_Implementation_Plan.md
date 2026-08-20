# **SKIN System Security Implementation Plan**
*Version 1.0 - 20 Agustus 2026*
*Status: Ready for Phase 1 Execution*

## **Executive Summary**

This plan outlines a 4-week comprehensive security enhancement initiative for the SKIN (Sistem Keuangan) application. The implementation follows an incremental, risk-based approach starting with the highest priority vulnerabilities. Each phase includes testing, deployment, and monitoring strategies to ensure system stability and security.

## **1. Risk Assessment & Priority Matrix**

| Vulnerability | Risk Level | Impact | Effort | Priority | Timeline |
|---------------|------------|---------|---------|-----------|-----------|
| **CSRF Missing** | HIGH | Account takeover, data corruption | LOW | **P1** | Week 1 |
| **Rate Limiting** | HIGH | Brute force attacks | MEDIUM | **P1** | Week 3 |
| **File Upload Security** | MEDIUM | Malware upload, server compromise | MEDIUM | **P2** | Week 2 |
| **Session Security** | MEDIUM | Session hijacking | LOW | **P2** | Week 4 |
| **XSS Vulnerabilities** | MEDIUM | Data theft, defacement | LOW | **P3** | Post-Phase 4 |
| **Password Policy** | LOW | Weak credential compromise | LOW | **P3** | Post-Phase 4 |

## **2. Phase 1: CSRF Universal Protection (Week 1)**

### **2.1 Goals & Objectives**
- ✅ Implement CSRF protection for ALL 34+ POST forms
- ✅ Zero functional regression
- ✅ User-friendly error handling (419 error page)
- ✅ AJAX/Fetch request compatibility

### **2.2 Technical Architecture**
**Approach**: Middleware-based implementation in `Router.php`

**Design Pattern**:
```
Request → Router → Security Middleware → Controller → Response
                    ↑
              CSRF Verification
              Rate Limiting (Week 3)
              Session Validation (Week 4)
```

### **2.3 Implementation Timeline**

#### **Day 1: Router Modification & Core Middleware** *(Estimated: 3 hours)*
**Tasks**:
1. Backup current codebase
2. Modify `app/core/Router.php` to add `applySecurityMiddleware()` method
3. Integrate middleware into `handlePostActions()` flow
4. Create CSRF verification wrapper with graceful error handling
5. Test basic GET/POST functionality

**Files to Modify**:
- `app/core/Router.php` - Primary middleware implementation
- `app/helpers/functions.php` - Enhance `csrf_verify()` with logging

**Success Criteria**:
- All GET requests unaffected by middleware
- POST requests trigger CSRF verification
- Invalid tokens return HTTP 419 error

#### **Day 2: Form Audit & Token Injection** *(Estimated: 4 hours)*
**Tasks**:
1. Create automated audit script to identify all POST forms
2. Add `<?php echo csrf_field(); ?>` to all 34+ POST forms
3. Create CSRF token validation test suite
4. Verify token uniqueness per session

**Critical Forms to Secure**:
1. `app/views/auth/login.php:285` - **HIGHEST PRIORITY**
2. `app/views/admin/tambah_user.php:32` - User management
3. `app/views/ormawa/tambah_pengajuan.php:221` - Proposal submission
4. `app/views/ormawa/upload_lpj.php:129` - LPJ upload
5. `app/views/admin/atur_saldo.php:49` - Balance management

**Script**:
```bash
# CSRF Form Audit Script
php scripts/audit_csrf_forms.php
```

#### **Day 3: AJAX Endpoint Protection** *(Estimated: 2 hours)*
**Tasks**:
1. Add CSRF token to JavaScript global variable
2. Modify AJAX/Fetch calls to include token in headers
3. Update notification system (`tandai_notif_baca`, `tandai_notif_terlihat`)
4. Test AJAX functionality with tokens

**JavaScript Implementation**:
```javascript
// In header.php or app.js
window.CSRF_TOKEN = "<?php echo csrf_token(); ?>";

// Auto-inject into fetch requests
const originalFetch = window.fetch;
window.fetch = function(url, options = {}) {
    if (options.method === 'POST') {
        options.headers = {
            ...options.headers,
            'X-CSRF-Token': window.CSRF_TOKEN
        };
    }
    return originalFetch(url, options);
};
```

#### **Day 4: Controller Validation Updates** *(Estimated: 3 hours)*
**Tasks**:
1. Verify all controller methods will accept CSRF validation via middleware
2. Update any direct CSRF checks to use middleware pattern
3. Ensure error handling consistency across controllers
4. Create controller test suite

**Controllers to Validate**:
- `PengajuanController` - All POST methods
- `UserController` - User management methods
- `VerifikasiController` - Already has partial CSRF
- `BendaharaController` - Already has CSRF
- `ProfilController` - Profile updates
- `InformasiController` - Announcements/schedules
- `AspirasiController` - Aspiration management

#### **Day 5: Testing & Validation** *(Estimated: 3 hours)*
**Testing Checklist**:
- [ ] All 34+ forms submit successfully with CSRF token
- [ ] Missing/invalid token shows 419 error
- [ ] No broken JavaScript functionality
- [ ] Back/Forward browser navigation works
- [ ] Multiple tabs work independently
- [ ] Session persistence maintained
- [ ] AJAX requests include tokens
- [ ] Form resubmission prevention

**Test Scenarios**:
1. **Normal Flow**: Form submission with valid token
2. **Security Test**: Form submission without token
3. **Tampering Test**: Form submission with modified token
4. **Concurrency Test**: Multiple forms in different tabs
5. **AJAX Test**: Notification marking with token
6. **Session Test**: Token regeneration after login

### **2.4 Risk Mitigation**
- **Backup Strategy**: Full codebase backup before Day 1
- **Rollback Plan**: Quick revert script available
- **Monitoring**: Log CSRF validation failures to `security.log`
- **Fallback Mechanism**: Config flag to temporarily disable if issues arise
- **Staging Environment**: Test on clone before production deployment

### **2.5 Success Metrics**
- **Quantitative**: 0 successful CSRF attacks post-implementation
- **Qualitative**: No user functionality broken, acceptable performance impact
- **Compliance**: All POST requests protected, error handling consistent

## **3. Phase 2: File Upload Security (Week 2)**

### **3.1 Goals & Objectives**
- Implement universal file validation across all upload types
- MIME type validation (not just extension checking)
- File size limits enforced consistently
- Secure filename handling and storage

### **3.2 Implementation Timeline**

#### **Day 1: Universal Validation Helper** *(Estimated: 3 hours)*
**Tasks**:
1. Create `validate_uploaded_file()` in `functions.php`
2. Implement MIME type detection using `finfo_open()`
3. Add file size validation
4. Create filename sanitization function

**Function Signature**:
```php
function validate_uploaded_file(
    array $file, 
    array $allowed_mime_types, 
    int $max_size_mb, 
    bool $sanitize_name = true
): array|false
```

#### **Day 2: Update Upload Handlers** *(Estimated: 4 hours)*
**Files to Update**:
1. `PengajuanController::tambah()` - Proposal PDF upload
2. `PengajuanController::edit()` - Revised proposal upload
3. `ProfilController::update()` - Profile image upload
4. `InformasiController::handlePengumuman()` - Announcement images
5. `app/views/ormawa/upload_lpj.php` - LPJ PDF upload
6. `app/views/ormawa/revisi_lpj.php` - Revised LPJ upload

**Validation Rules**:
| File Type | Max Size | Allowed MIME Types | Directory |
|-----------|----------|-------------------|-----------|
| Proposal PDF | 5MB | `application/pdf` | `/uploads/proposal/` |
| Profile Image | 2MB | `image/jpeg`, `image/png`, `image/gif` | `/uploads/profil/` |
| LPJ PDF | 10MB | `application/pdf` | `/uploads/lpj/` |
| System Logo | 1MB | `image/*` | `/uploads/logo/` |
| Regulation PDF | 5MB | `application/pdf` | `/uploads/regulasi/` |

#### **Day 3: Filename Sanitization & Storage** *(Estimated: 2 hours)*
**Tasks**:
1. Implement secure filename generation
2. Add path traversal protection
3. Set proper directory permissions (755 for dirs, 644 for files)
4. Add `.htaccess` to prevent direct execution in upload directories

**Filename Generation**:
```php
function generate_safe_filename(
    string $original_name, 
    string $prefix = '', 
    int $user_id = 0
): string
// Example: proposal_123_1734697358_a1b2c3d4.pdf
```

#### **Day 4: Testing & Validation** *(Estimated: 3 hours)*
**Test Cases**:
1. Valid PDF upload (should pass)
2. PDF with wrong extension (should fail MIME check)
3. Malicious file disguised as PDF (should fail)
4. Oversized file upload (should fail)
5. Directory traversal attempt (should be sanitized)
6. Concurrent uploads from same user

#### **Day 5: Monitoring & Logging** *(Estimated: 1 hour)*
**Tasks**:
1. Implement upload attempt logging
2. Create quarantine directory for suspicious files
3. Add admin notification for repeated failed uploads
4. Update documentation with new file requirements

### **3.3 Risk Mitigation**
- **Fallback**: If `finfo_open()` unavailable, use extension check with warning
- **Quarantine**: Move suspicious files to isolated directory for review
- **Logging**: Detailed upload logs with validation results
- **Size Limits**: Configurable via admin settings

## **4. Phase 3: Rate Limiting (Week 3)**

### **4.1 Goals & Objectives**
- Prevent brute force login attacks
- Limit form submission spam
- Protect API endpoints from abuse
- Maintain legitimate user access

### **4.2 Implementation Timeline**

#### **Day 1: Database Schema & Core Functions** *(Estimated: 3 hours)*
**Tasks**:
1. Create `login_attempts` table
2. Implement `check_rate_limit()` function
3. Add IP-based and user-based tracking
4. Create cleanup cron job for old records

**Table Schema**:
```sql
CREATE TABLE login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(100),
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    user_agent TEXT,
    action VARCHAR(50) DEFAULT 'login',
    
    INDEX idx_ip_time (ip_address, attempted_at),
    INDEX idx_username_time (username, attempted_at),
    INDEX idx_action_time (action, attempted_at)
);
```

#### **Day 2: Login Rate Limiting** *(Estimated: 3 hours)*
**Tasks**:
1. Integrate rate limiting into `auth/login.php`
2. Track failed attempts per IP and username
3. Implement account lockout (5 failed attempts → 15 min cooldown)
4. Clear successful attempt counters

**Rate Limits**:
- `login`: 5 attempts per 15 minutes per IP
- `login_username`: 3 attempts per 15 minutes per username
- Progressive delay for repeated failures

#### **Day 3: Form Submission Rate Limiting** *(Estimated: 3 hours)*
**Tasks**:
1. Extend rate limiting to critical forms
2. Implement per-action limits
3. Add user-friendly error messages
4. Create admin whitelist for testing

**Protected Actions**:
| Action | Limit | Window | Scope |
|--------|-------|---------|-------|
| `form_submission` | 20 | 1 minute | IP-based |
| `proposal_submit` | 5 | 10 minutes | User-based |
| `password_reset` | 3 | 1 hour | IP+Email |
| `api_request` | 100 | 1 hour | IP-based |

#### **Day 4: User Experience & Messaging** *(Estimated: 2 hours)*
**Tasks**:
1. Design user-friendly rate limit messages
2. Implement cooldown countdown display
3. Add "I'm not a robot" verification option
4. Create admin override capability

**Messages**:
- "Too many login attempts. Please try again in 14 minutes 32 seconds."
- "Rate limit exceeded for form submissions. Please wait before trying again."
- "Account temporarily locked. Contact administrator or try again later."

#### **Day 5: Testing & Monitoring** *(Estimated: 2 hours)*
**Tasks**:
1. Simulate brute force attacks
2. Verify legitimate users not blocked
3. Test whitelist functionality
4. Monitor performance impact
5. Create rate limit dashboard for admins

### **4.3 Risk Mitigation**
- **Whitelist**: Admin IPs and test accounts exempt
- **Emergency Bypass**: Config flag to disable during issues
- **Monitoring Dashboard**: Real-time visualization of attempts
- **Alerting**: Email/SMS alerts for attack patterns

## **5. Phase 4: Session Security (Week 4)**

### **5.1 Goals & Objectives**
- Implement session timeout after inactivity
- Prevent session fixation attacks
- Secure cookie handling (HttpOnly, Secure, SameSite)
- Regenerate session IDs periodically

### **5.2 Implementation Timeline**

#### **Day 1: Enhanced Session Configuration** *(Estimated: 2 hours)*
**Tasks**:
1. Update `initialize_session()` in `functions.php`
2. Configure secure cookie parameters
3. Implement session ID regeneration
4. Add session creation timestamp

**Cookie Configuration**:
```php
session_set_cookie_params([
    'lifetime' => 0, // Browser session
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? '',
    'secure' => isset($_SERVER['HTTPS']), // Auto-detect
    'httponly' => true,
    'samesite' => 'Strict'
]);
```

#### **Day 2: Session Timeout & Validation** *(Estimated: 3 hours)*
**Tasks**:
1. Implement inactivity timeout (30 minutes)
2. Add session validation middleware
3. Create session destruction on timeout
4. Implement "Remember me" optional extension

**Inactivity Logic**:
```php
// In check_login() or middleware
$timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    redirect('index.php?page=login&error=session_expired');
}
$_SESSION['last_activity'] = time();
```

#### **Day 3: Concurrent Session Control** *(Estimated: 2 hours)*
**Tasks**:
1. Add `session_id` column to `users` table
2. Implement single session per user (optional)
3. Create session management for admins
4. Add "Logout other devices" feature

**Optional Feature**:
```sql
ALTER TABLE users ADD COLUMN 
current_session_id VARCHAR(128) DEFAULT NULL;
```

#### **Day 4: Security Headers & Hardening** *(Estimated: 2 hours)*
**Tasks**:
1. Add security headers in `config.php`
2. Implement Content Security Policy (CSP)
3. Add XSS protection headers
4. Create header validation test

**Security Headers**:
```php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
// Optional CSP
header("Content-Security-Policy: default-src 'self'");
```

#### **Day 5: Testing & User Experience** *(Estimated: 3 hours)*
**Tasks**:
1. Test session timeout functionality
2. Verify cookie security flags
3. Test concurrent login handling
4. Validate "Remember me" feature
5. User acceptance testing

**Test Scenarios**:
1. Inactivity timeout after 30 minutes
2. Session regeneration on privilege escalation
3. Cookie attributes inspection
4. Concurrent session prevention
5. Logout functionality

### **5.3 Risk Mitigation**
- **Graceful Timeout**: Warning before session expiration
- **Activity Extension**: Reset timer on user interaction
- **Multiple Device Support**: Configurable concurrent sessions
- **Fallback**: Basic session if secure features fail

## **6. Testing Strategy**

### **6.1 Automated Test Suite**
**Directory Structure**:
```
tests/
├── security/
│   ├── CsrfTest.php
│   ├── FileUploadTest.php
│   ├── RateLimitTest.php
│   ├── SessionTest.php
│   └── IntegrationTest.php
├── functional/
│   └── FormSubmissionTest.php
└── bootstrap.php
```

**Test Coverage Goals**:
- CSRF: 100% of POST endpoints
- File Upload: All upload handlers
- Rate Limiting: Critical actions
- Session: Core security features

### **6.2 Manual Testing Checklist**
**Pre-Deployment Checklist**:
- [ ] All forms submit with CSRF tokens
- [ ] File uploads validate MIME types
- [ ] Rate limiting blocks excessive attempts
- [ ] Sessions timeout correctly
- [ ] No regression in existing functionality
- [ ] Performance impact < 10%

**Post-Deployment Monitoring**:
- [ ] Security log entries created
- [ ] No false positive blocks
- [ ] User feedback collected
- [ ] System performance stable

### **6.3 Penetration Testing**
**Basic Security Tests**:
1. **CSRF**: Submit forms without tokens, modify tokens
2. **File Upload**: Upload malicious files, test MIME bypass
3. **Rate Limiting**: Attempt brute force login
4. **Session**: Test fixation, hijacking attempts
5. **XSS**: Attempt injection in form fields

**Tools**:
- OWASP ZAP for automated scanning
- Custom scripts for specific vulnerabilities
- Manual exploration by security team

## **7. Deployment Plan**

### **7.1 Environment Strategy**
```
Development → Staging → Production
    ↓           ↓          ↓
   Test    UAT/Validation  Live
```

**Staging Environment Requirements**:
- Clone of production database (sanitized)
- Identical server configuration
- Test user accounts
- Monitoring tools enabled

### **7.2 Deployment Schedule**
**Week 1 - CSRF Protection**:
- Monday: Deploy to staging
- Tuesday: User acceptance testing
- Wednesday: Fix issues
- Thursday: Deploy to production (off-peak hours)
- Friday: Monitor & adjust

**Week 2-4**: Repeat pattern for each phase

### **7.3 Rollback Procedures**
**Emergency Rollback Script**:
```bash
#!/bin/bash
# rollback_security.sh
BACKUP_DIR="/backups/security_$(date +%Y%m%d)"
RESTORE_DIR="/var/www/sistem_keuangan"

echo "Starting rollback..."
cp -r $BACKUP_DIR/* $RESTORE_DIR/
service apache2 restart
echo "Rollback complete. Check system functionality."
```

**Rollback Triggers**:
1. Critical functionality broken
2. Performance degradation > 20%
3. User complaints > 5% of user base
4. Security feature causing false positives

## **8. Monitoring & Maintenance**

### **8.1 Security Logging**
**Log File**: `/var/log/skin/security.log`
**Log Format**: `[TIMESTAMP] [LEVEL] [CATEGORY]: [MESSAGE]`

**Example Entries**:
```
[2026-08-20 14:30:00] [WARN] [CSRF]: Invalid token from 192.168.1.100 on /login
[2026-08-20 14:31:00] [INFO] [RATE_LIMIT]: IP 192.168.1.101 exceeded login attempts
[2026-08-20 14:32:00] [ERROR] [FILE_UPLOAD]: MIME mismatch for user 123
```

### **8.2 Alerting System**
**Email Alerts**:
- Multiple CSRF failures from same IP
- Brute force attack detected
- Suspicious file upload attempts
- System performance issues

**SMS Alerts** (Critical):
- DDoS attack detection
- Database compromise attempts
- System downtime

### **8.3 Regular Maintenance**
**Weekly**:
- Review security logs
- Check for failed login patterns
- Verify backup integrity
- Update security rules if needed

**Monthly**:
- Security audit review
- Penetration test results analysis
- User feedback review
- Performance metrics evaluation

**Quarterly**:
- Third-party security audit
- Update security policies
- Team security training
- Incident response drill

## **9. Success Metrics & KPIs**

### **9.1 Quantitative Metrics**
| Metric | Target | Measurement |
|--------|---------|-------------|
| CSRF Attacks Blocked | 100% | Security logs |
| Failed Login Reduction | >50% | Login attempt data |
| Malicious Uploads Prevented | >90% | File upload logs |
| Session Hijacking Attempts | 0 | Session logs |
| False Positive Rate | <1% | User reports |

### **9.2 Qualitative Metrics**
- User satisfaction with security features
- Admin confidence in system security
- Compliance with security standards
- Positive security audit results
- Reduced support tickets for security issues

### **9.3 Performance Metrics**
- Page load time increase: <10%
- Database query impact: <5%
- Memory usage increase: <15%
- Session management overhead: <3%

## **10. Resource Requirements**

### **10.1 Team Allocation**
| Role | Hours/Week | Total Hours | Responsibilities |
|------|------------|-------------|------------------|
| Lead Developer | 20 | 80 | Implementation, Code Review |
| QA Engineer | 15 | 60 | Testing, Validation |
| Security Analyst | 10 | 40 | Audit, Penetration Testing |
| System Admin | 5 | 20 | Deployment, Monitoring |
| **Total** | **50** | **200** | |

### **10.2 Technical Requirements**
**Software**:
- PHP 8.2+ with FileInfo extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ or Nginx 1.18+
- Git for version control

**Hardware**:
- Staging server identical to production
- Sufficient storage for security logs
- Backup storage system
- Monitoring server (optional)

### **10.3 Budget Estimate**
| Item | Cost | Notes |
|------|------|-------|
| Development Hours | $X,XXX | Based on 200 hours |
| Security Tools | $XXX | Optional commercial tools |
| Third-party Audit | $X,XXX | Quarterly audit |
| Training | $XXX | Team security training |
| **Total** | **$X,XXX** | |

## **11. Appendix**

### **11.1 File Inventory**
**Critical Files for Security**:
1. `app/core/Router.php` - Middleware implementation
2. `app/helpers/functions.php` - Security functions
3. `app/controllers/*.php` - Controller security
4. `app/views/**/*.php` - Form security
5. `config.php` - Security configuration
6. `assets/js/app.js` - Client-side security

### **11.2 Dependencies**
**PHP Extensions Required**:
- `fileinfo` - MIME type detection
- `openssl` - CSRF token generation
- `session` - Session management
- `pdo_mysql` - Database access

**JavaScript Libraries**:
- Bootstrap 5.3 (existing)
- Custom security utilities (to be created)

### **11.3 Glossary**
- **CSRF**: Cross-Site Request Forgery
- **MIME**: Multipurpose Internet Mail Extensions
- **XSS**: Cross-Site Scripting
- **CSP**: Content Security Policy
- **HttpOnly**: Cookie flag preventing JavaScript access
- **SameSite**: Cookie flag restricting cross-site requests

### **11.4 Change Log**
| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | 2026-08-20 | Initial implementation plan | Security Team |
| 1.1 | TBD | Post-Phase 1 updates | TBD |
| 1.2 | TBD | Post-Phase 2 updates | TBD |

---

## **Approval & Sign-off**

**Project Sponsor**: _________________________
**Technical Lead**: _________________________
**Security Officer**: _________________________
**Quality Assurance**: _________________________

**Approval Date**: _________________________
**Next Review Date**: _________________________

---

*This document is living and will be updated throughout the implementation process. All team members should refer to the latest version available in the project documentation repository.*