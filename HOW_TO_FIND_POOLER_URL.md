# HOW TO FIND YOUR SUPABASE CONNECTION POOLER URL

## You're in the Wrong Section!

You're currently in **Settings → Database → Connection Pooling** which shows configuration options, but you need the **actual connection string**.

## Correct Steps to Find Connection Pooler URL:

### Step 1: Go to Connection Info
1. In your Supabase Dashboard
2. Go to **Settings** → **Database** 
3. Look for **"Connection Info"** section (NOT "Connection Pooling")
4. You should see tabs like:
   - **"Connection string"** (direct connection - IPv6 only)
   - **"Connection pooling"** (this is what you need!)

### Step 2: Click on "Connection pooling" Tab
- Click the **"Connection pooling"** tab
- You'll see a connection string that looks like:
  ```
  postgresql://postgres:[YOUR-PASSWORD]@aws-0-us-west-1.pooler.supabase.com:6543/postgres
  ```

### Step 3: Copy the Pooler Connection String
- Copy the **entire connection string** from the "Connection pooling" tab
- It will have a different hostname than `db.byjigntaprgwvvfodirl.supabase.co`
- It will use port **6543**

## Alternative Method:

### If You Can't Find Connection Pooling Tab:
1. Go to **Settings** → **Database**
2. Look for **"Database URL"** or **"Connection string"**
3. There should be **two options**:
   - Direct connection (IPv6 - won't work on Render)
   - Pooled connection (IPv4 - this is what you need)

## What You're Looking For:

**WRONG (Direct connection - IPv6 only):**
```
postgresql://postgres:J1eEYfAYvnZyjxTO@db.byjigntaprgwvvfodirl.supabase.co:5432/postgres
```

**RIGHT (Pooled connection - IPv4):**
```
postgresql://postgres:J1eEYfAYvnZyjxTO@aws-0-us-west-1.pooler.supabase.com:6543/postgres
```

## If You Still Can't Find It:

The connection pooler URL format is usually:
- `aws-0-[region].pooler.supabase.com:6543`
- Examples:
  - `aws-0-us-west-1.pooler.supabase.com:6543`
  - `aws-0-eu-west-1.pooler.supabase.com:6543`
  - `aws-0-ap-southeast-1.pooler.supabase.com:6543`

## Next Steps:
1. Find the **"Connection pooling"** tab in Database settings
2. Copy the pooled connection string
3. Update your Render `DATABASE_URL` with that string
4. Deploy and test

The key is finding the **pooled connection string**, not the configuration page you're currently on!
