# MediConnect - Medical Consultation & Discussion Platform

<p align="center">
  <img src="https://img.shields.io/badge/Built%20with-Laravel-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Frontend-Blade-34495e?style=for-the-badge" alt="Blade">
  <img src="https://img.shields.io/badge/Language-PHP-777BB4?style=for-the-badge&logo=php" alt="PHP">
</p>

---

## 📋 Maelezo ya Mfumo

**MediConnect** ni jukwaa la kidijitali la kujadiliana kwa madaktari, wataalamu maalum (specialists) na wamimikazi wa utawala wa ngoma. Mfumo huu umeundwa kusambaza ujumbe wa kimatibabu, kujifunza kwa pamoja, na kusambaza maarifa kuhusu visa vya ngoma ngumu.

---

## 🎯 Madhumuni Makuu

1. **Kujadiliana Kwa Wazi** - Daktari anayepata kesi ngumu anaweza kuomba msaada kwa wote wanaofanya kazi
2. **Changia Kwa Haba** - Wazazi wote wanaweza kujibu na kushiriki mawazo yao
3. **Kuandika Kwa Wazi** - Kila jibu linaonekana kwa timu nzima
4. **Usiri wa Mgonjwa** - Taarifa za mgonjwa ni VIP/siri na zinakubaliwa kwa ajili ya kumbukumbu

---

## 👥 Watumiaji wa Mfumo (3 Roles)

| Role | Kazi |
|------|------|
| **👨‍⚕️ Daktari** | Kuingiza mgonjwa, kuomba msaada kwa kesi ngumu, kujibu maswali ya wadaktari wengine |
| **🏥 Mtaalamu Maalum (Specialist)** | Kujibu maswali ya kesi ngumu, kutoa njia za matibabu, kushiriki ujumbe |
| **⚙️ Msimamizi (Admin)** | Kusimamia watumiaji, kuangalia mfumo, kujenga ripoti |

---

## 🔄 Jinsi Mfumo Unavyofanya Kazi

### 1️⃣ **Kuingia na Kujenga Mgonjwa**
```
Daktari anaingia (Email + Password)
         ↓
Daktari anasajiri mgonjwa (jina, ID, dalili)
         ↓
Taarifa za mgonjwa zihifadhiwa VIP (Private/Encrypted)
```

### 2️⃣ **Kuomba Msaada - Kesi Ngumu**
```
Daktari anaiona kesi ngumu
         ↓
Daktari anazalisha "Post" (swali/mahitaji msaada)
         ↓
NOTIFICATION inatumwa kwa WOTE (wote madaktari na specialists)
         ↓
Wote wanaweza kujibu haraka
```

### 3️⃣ **Kujibu na Kujadiliana**
```
Mtaalamu anakubaliana ↓ Jibu lake linaonekana kwa wote
Daktari mwingine anajibu ↓ Jibu lake linaonekana kwa wote
Madaktari yanajadiliana ↓ Kila jibu linaonekana (Multi-threaded)
         ↓
MATOKEO: Mgonjwa huona majibu mengi + maoni mbalimbali
```

### 4️⃣ **Usalama wa Taarifa za Mgonjwa**
```
✅ Jina la Mgonjwa - Recorded SEPARATELY (VIP)
✅ ID ya Mgonjwa - Recorded SEPARATELY (VIP)
✅ Dalili/Ugonjwa - Recorded SEPARATELY (VIP)

❌ JINA / ID HAIANGUKII kwenye post za umma
❌ Kila mtu akishangilia data - LOG inaandikwa

MATOKEO: Wote wanapambana viongozi, lakini mgonjwa anaonekana kuwa "CASE ID" tu
```

---

## 🏗️ Muundo wa Mfumo (Architecture)

### **Database Tables**

```
┌─────────────────────────────────────────────────────────┐
│                    USERS                                │
├─────────────────────────────────────────────────────────┤
│ id | name | email | password | role | created_at       │
│    |      |       |          |      |                   │
│ 1  | Dr. A | dr.a@... | hash | doctor | 2025-01-15    │
│ 2  | Spec. B | spec.b@... | hash | specialist | ...    │
│ 3  | Admin | admin@... | hash | admin | ...            │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│                  PATIENTS (VIP DATA)                    │
├─────────────────────────────────────────────────────────┤
│ id | patient_name | patient_id | symptoms | doctor_id  │
│    |              |            |          |            │
│ 1  | John Doe     | P001       | Fever    | 1 (Dr.A)   │
└─────────────────────────────────────────────────────────┘
       ↓
    PRIVATE STORAGE
    (Only doctor who created can view full details)

┌─────────────────────────────────────────────────────────┐
│              CONSULTATION POSTS (Public)                │
├─────────────────────────────────────────────────────────┤
│ id | case_ref | title | description | doctor_id |      │
│    |          |       |             |           | time  │
│ 1  | CASE#001 | Mgirango Newborn | Maelezo... | 1   │...│
└─────────────────────────────────────────────────────────┘
       ↓
    PUBLIC DISCUSSION
    (All doctors/specialists see this)

┌─────────────────────────────────────────────────────────┐
│              CONSULTATION REPLIES (Public)              │
├─────────────────────────────────────────────────────────┤
│ id | post_id | specialist_id | reply_text | created_at │
│    |         |               |            |            │
│ 1  | 1       | 2             | Jibu...    | 2025-01-16 │
│ 2  | 1       | 3             | Jibu...    | 2025-01-16 │
└─────────────────────────────────────────────────────────┘
       ↓
    MULTI-THREADED
    (Many can reply - open discussion)

┌─────────────────────────────────────────────────────────┐
│           AUDIT LOGS (Usalama/Security)                 │
├─────────────────────────────────────────────────────────┤
│ id | user_id | action | patient_id | timestamp | status │
│    |         |        |            |           |        │
│ 1  | 1       | VIEW   | P001       | 2025-01-16| ✓     │
│ 2  | 2       | VIEW   | P001       | 2025-01-16| ✓     │
└─────────────────────────────────────────────────────────┘
       ↓
    Kila mtu akikumbuka mgonjwa - LOGGED
```

---

## 🔐 Usalama wa Taarifa

### **Taarifa ya Mgonjwa (VIP)**
- ✅ Jina halisi → **ENCRYPTED**
- ✅ ID ya Mgonjwa → **ENCRYPTED**
- ✅ Dalili/Ugonjwa → **ENCRYPTED**
- ✅ Kila mtu akikubaliana data → **LOGGED**

### **Taarifa ya Discussion (Public)**
- ✅ CASE REFERENCE ONLY (e.g., CASE#001)
- ✅ **HAJANA** jina au ID ya mgonjwa
- ✅ Wote wanaweza kujibu
- ✅ Kila jibu linakubaliwa

### **Matokeo**
- 👀 Wote huona "CASE#001 - Mgirango Newborn"
- 🔒 **LAKINI** taarifa za mgonjwa zimefungwa
- 📝 Kila mtu akikubaliana na patient data - AUDIT LOG

---

## 📊 Kesi za Matumizi

### **Kesi 1: Mgemu wa Newborn**
```
Dr. Ahmed: "CASE#001 - Mgirango Newborn - ??"
           ↓
Spec. Sarah: "Jaribusaini Bilirubin test..."
           ↓
Dr. Ahmed: "+1, nami nilijaribu..."
           ↓
Spec. John: "Mimi naafisa..."
           ↓
Dr. Ahmed: "Asante sana, tutajaribu..."
```
✅ Multi-threaded discussion
✅ Wote wanajue kesi hiyo
✅ Jina la mgonjwa LILIKOFA

### **Kesi 2: Usalama**
```
Dr. Ahmed akakumbuka "John Doe" (P001)
           ↓
AUDIT LOG: "Dr. Ahmed - VIEW - P001 - 2025-01-16 10:30 AM"
           ↓
Admin anakubaliana log
           ↓
Kama John Doe pia anataka kujua - shida inakuwa CLEAR
```
✅ Kuandika kila mmoja akikumbuka mgonjwa
✅ Usalama + Kusumatia

---

## 🛠️ Teknolohia

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templates
- **Database**: MySQL/PostgreSQL
- **Authentication**: Email + Password
- **Features**: Real-time notifications, Multi-user discussion, Audit logging

---

## 📝 Mabadilisho Kati ya Old vs New System

| Aspekt | Mfumo wa Kale | Mfumo Mpya |
|--------|--------------|-----------|
| **Kugawa Kesi** | Admin huassign kwa Specialist mmoja | Wote wanaweza kujibu (no assignment) |
| **Notifications** | Single recipient | All doctors + specialists |
| **Kujibu** | Specialist mmoja tu | Multiple doctors/specialists |
| **Discussion** | Linear (Q→A) | Multi-threaded |
| **Taarifa za Mgonjwa** | Inaweza kuonekana | VIP/Encrypted - Private reference only |
| **Case Visibility** | Private (assigned tu) | Public (open discussion) |

---

## 🚀 Installation & Setup

```bash
# 1. Clone repository
git clone https://github.com/RogerEugen/mediconnect.git

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Create admin user
php artisan tinker
> User::create(['name' => 'Admin', 'email' => 'admin@mediconnect.local', 'password' => bcrypt('password'), 'role' => 'admin'])

# 7. Start server
php artisan serve
```

---

## 📞 Support

Kwa maswali au huduma, wasiliana na: **RogerEugen**

---

## 📄 License

MIT License - Tafadhali angalia `LICENSE` file kwa maelezo zaidi.
