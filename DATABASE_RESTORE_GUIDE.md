# Database Restore Guide

## Overview

For security reasons, database restore functionality is **not available** through the phpCollab web interface. This document provides instructions for restoring database backups using command-line tools or external administration software.

**Why no web-based restore?**
- High security risk (file upload + SQL execution)
- Potential for SQL injection attacks
- Denial of service vulnerabilities
- Code execution vectors
- Best practice: Restrict privileged operations to authenticated system administrators with shell access

---

## MySQL / MariaDB Restore

### Command Line (Recommended)

**1. Using mysql command:**
```bash
# Uncompressed SQL file
mysql -u username -p database_name < backup_file.sql

# Compressed SQL file (.gz)
gunzip < backup_file.sql.gz | mysql -u username -p database_name

# With specific host
mysql -h hostname -u username -p database_name < backup_file.sql
```

**2. Using mysqldump with --all-databases backup:**
```bash
mysql -u username -p < full_backup.sql
```

**Example:**
```bash
# Restore phpCollab database from backup
mysql -u root -p phpcollab < phpcollab_2025_11_08.sql
```

### External Tools

**Adminer** (Recommended)
- Download: https://www.adminer.org/
- Single PHP file, secure, actively maintained
- Features: Import SQL files, run queries, manage tables
- Usage: Upload adminer.php to secure directory, access via browser

**phpMyAdmin**
- Official: https://www.phpmyadmin.net/
- Features: Full database management, import/export
- Note: Use latest version (not bundled versions)

**MySQL Workbench** (Desktop)
- Download: https://www.mysql.com/products/workbench/
- GUI tool for database administration
- Import via: Server → Data Import → Import from Self-Contained File

---

## PostgreSQL Restore

### Command Line (Recommended)

**1. Using psql command:**
```bash
# Uncompressed SQL file
psql -U username -d database_name -f backup_file.sql

# Compressed SQL file (.gz)
gunzip < backup_file.sql.gz | psql -U username -d database_name

# With specific host
psql -h hostname -U username -d database_name -f backup_file.sql
```

**2. Create database before restore (if needed):**
```bash
createdb -U username database_name
psql -U username -d database_name -f backup_file.sql
```

**Example:**
```bash
# Restore phpCollab database from backup
psql -U postgres -d phpcollab -f phpcollab_2025_11_08.sql
```

### External Tools

**pgAdmin 4** (Recommended)
- Download: https://www.pgadmin.org/
- Official PostgreSQL administration tool
- Features: Import SQL files, query tool, schema management
- Restore via: Right-click database → Restore

**Adminer**
- Works with PostgreSQL too
- Download: https://www.adminer.org/

**DBeaver** (Desktop, Multi-platform)
- Download: https://dbeaver.io/
- Supports PostgreSQL, MySQL, SQL Server, and more
- Import via: Tools → Execute SQL Script

---

## SQL Server Restore

### Command Line (Recommended)

**1. Using sqlcmd:**
```bash
# Uncompressed SQL file
sqlcmd -S server_name -d database_name -i backup_file.sql

# With authentication
sqlcmd -S server_name -U username -P password -d database_name -i backup_file.sql

# Using Windows authentication
sqlcmd -S server_name -E -d database_name -i backup_file.sql
```

**2. Create database before restore (if needed):**
```bash
sqlcmd -S server_name -Q "CREATE DATABASE database_name"
sqlcmd -S server_name -d database_name -i backup_file.sql
```

**Example:**
```bash
# Restore phpCollab database from backup
sqlcmd -S localhost -E -d phpcollab -i phpcollab_2025_11_08.sql
```

### External Tools

**SQL Server Management Studio (SSMS)** (Recommended)
- Download: https://docs.microsoft.com/en-us/sql/ssms/download-sql-server-management-studio-ssms
- Official Microsoft tool for SQL Server
- Features: Full database management, import/export, query editor
- Restore via: File → Open → File, then Execute

**Azure Data Studio** (Cross-platform)
- Download: https://docs.microsoft.com/en-us/sql/azure-data-studio/download-azure-data-studio
- Modern, lightweight alternative to SSMS
- Works on Windows, macOS, Linux

**Adminer**
- Supports SQL Server via sqlsrv/mssql drivers
- Download: https://www.adminer.org/

---

## Security Best Practices

### Before Restoring

1. **Verify backup integrity**
   ```bash
   # Check file is readable and not corrupted
   head -n 20 backup_file.sql
   ```

2. **Review backup contents**
   ```bash
   # Look for suspicious commands
   grep -i "LOAD DATA\|OUTFILE\|DUMPFILE" backup_file.sql
   ```

3. **Test in non-production environment first**

### During Restore

1. **Use dedicated database user with limited privileges**
2. **Backup current database before restore**
   ```bash
   # MySQL
   mysqldump -u root -p database_name > pre_restore_backup.sql

   # PostgreSQL
   pg_dump -U postgres database_name > pre_restore_backup.sql

   # SQL Server
   # Use phpCollab backup feature or SSMS
   ```

3. **Monitor restore process for errors**

### After Restore

1. **Verify data integrity**
   ```sql
   -- Check table counts
   SELECT COUNT(*) FROM users;
   SELECT COUNT(*) FROM projects;
   ```

2. **Test application functionality**

3. **Review logs for errors**

---

## Troubleshooting

### "Access denied" errors
- Check username/password
- Verify user has appropriate privileges
- For MySQL: `GRANT ALL ON database_name.* TO 'user'@'localhost';`
- For PostgreSQL: Check pg_hba.conf authentication settings

### "Database does not exist" errors
- Create database first before restore
- MySQL: `CREATE DATABASE database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
- PostgreSQL: `CREATE DATABASE database_name ENCODING 'UTF8';`
- SQL Server: `CREATE DATABASE database_name;`

### "Table already exists" errors
- Backup was created without "drop table" option
- Either: Drop existing database and recreate, or manually drop tables first

### "Out of memory" errors
- Large backups may need increased memory limits
- Restore in smaller chunks or increase PHP/MySQL memory limits
- Consider using command line instead of web tools

### Permission errors on imported data
- Check file ownership and permissions
- Verify database user has INSERT privileges
- For LOAD DATA INFILE: Check secure_file_priv settings

---

## Additional Resources

- **MySQL Documentation**: https://dev.mysql.com/doc/refman/8.0/en/mysql.html
- **PostgreSQL Documentation**: https://www.postgresql.org/docs/current/app-psql.html
- **SQL Server Documentation**: https://docs.microsoft.com/en-us/sql/tools/sqlcmd-utility

---

## Support

For phpCollab-specific issues, please visit:
- GitHub Issues: https://github.com/phpcollab/phpcollab/issues
- Documentation: https://www.phpcollab.com/

**Note**: Always test restore procedures in a non-production environment before performing production restores.
