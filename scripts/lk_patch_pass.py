#!/usr/bin/env python3
"""
Patches the $this->pass assignment in /tmp/lkrentacar_db.php with the value
from the LKRENTACAR_DB_PASSWORD environment variable.

- Handles both single-quoted and double-quoted existing values, including
  those that contain escaped characters (\' or \\).
- PHP-escapes the new password for a single-quoted PHP string literal.
- Uses a callable replacement so Python never reprocesses backslashes.
- Exits with code 1 on error (not found, multiple found).
- Exits with code 0 and writes the patched file on success.
"""
import re
import os
import sys

content = open('/tmp/lkrentacar_db.php').read()
new_pass = os.environ.get('LKRENTACAR_DB_PASSWORD', '')
if not new_pass:
    sys.stderr.write("ERROR: LKRENTACAR_DB_PASSWORD environment variable is empty or unset\n")
    sys.exit(1)

# PHP-escape for a single-quoted string literal: escape \ then '
php_escaped = new_pass.replace('\\', '\\\\').replace("'", "\\'")

# Escape-aware pattern: matches existing single OR double-quoted value,
# including any escaped chars inside (e.g. \' or \\).
# The full assignment including trailing ; is matched so indentation is preserved.
pattern = r"""\$this->pass\s*=\s*(?:'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*")\s*;"""


# Callable replacement: Python never reprocesses the return value for escapes.
def make_replacement(m):
    return "$this->pass = '" + php_escaped + "';"


patched, count = re.subn(pattern, make_replacement, content)
if count == 0:
    sys.stderr.write("ERROR: no $this->pass assignment found in Database.php\n")
    sys.exit(1)
if count > 1:
    sys.stderr.write("ERROR: " + str(count) + " $this->pass assignments found (expected exactly 1)\n")
    sys.exit(1)

open('/tmp/lkrentacar_db.php', 'w').write(patched)
print("Patched 1 assignment to single-quoted form, escaped length " + str(len(php_escaped)))
