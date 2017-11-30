# SQL Migration task directory

This directory contains the migration files for the database.

This is the prefered way to update the database:

**Check current status:**

```bash
./bin/console migrate status
```

**Execute all pending migrations:**

```bash
./bin/console migrate all
```

**Use --help to view all the options:**

```bash
./bin/console migrate -h
```
