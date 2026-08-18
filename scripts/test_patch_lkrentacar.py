#!/usr/bin/env python3
"""
Unit tests for the patch-lkrentacar password-replacement logic.

Tests cover the same code path used in the workflow's inline Python block:
  - PHP-escaping of \ and '
  - re.subn with a callable replacement (no double-processing of backslashes)
  - Both single-quoted and double-quoted $this->pass forms
  - Special characters: apostrophe, single backslash, consecutive backslashes,
    trailing backslash, combinations.
"""
import re
import sys


def php_escape(password: str) -> str:
    """PHP-escape a string for use inside single quotes: escape \\ then '."""
    return password.replace('\\', '\\\\').replace("'", "\\'")


def patch_pass(php_content: str, new_pass: str) -> tuple[str, int]:
    """
    Replace the $this->pass assignment in PHP content.
    Returns (patched_content, replacement_count).
    Uses a callable so Python does NOT reprocess backslashes in the replacement.
    """
    escaped = php_escape(new_pass)
    pattern = r"""(\$this->pass\s*=\s*)(['"])[^'"]*\2"""

    def make_replacement(m):
        return m.group(1) + "'" + escaped + "'"

    return re.subn(pattern, make_replacement, php_content)


# ---------------------------------------------------------------------------
# Test helpers
# ---------------------------------------------------------------------------

PASS_ASSIGN_TEMPLATE = """\
<?php
class Database {{
    public function __construct() {{
        $this->host = 'localhost';
        $this->user = 'u144787244_lkrentacar';
        $this->pass = {value};
        $this->ddbb = 'u144787244_lkrentacar';
    }}
}}
"""


def make_php(quoted_value: str) -> str:
    """quoted_value should include the surrounding quotes, e.g. \"'secret'\" or '\"secret\"'."""
    return PASS_ASSIGN_TEMPLATE.format(value=quoted_value + ";")


def extract_pass(php_content: str) -> str:
    """Pull back the raw (unescaped) value between the quotes after patching."""
    m = re.search(r"""\$this->pass\s*=\s*'((?:[^'\\]|\\.)*)'\s*;""", php_content, re.DOTALL)
    if not m:
        raise AssertionError("No $this->pass found in:\n" + php_content)
    # Reverse single-quote PHP escaping to get the original password back
    raw = m.group(1).replace("\\'", "'").replace('\\\\', '\\')
    return raw


def run_test(name: str, original_quoted: str, new_pass: str):
    php = make_php(original_quoted)
    patched, count = patch_pass(php, new_pass)
    assert count == 1, f"[{name}] expected 1 replacement, got {count}"
    recovered = extract_pass(patched)
    assert recovered == new_pass, (
        f"[{name}] FAIL\n"
        f"  new_pass (repr):  {new_pass!r}\n"
        f"  recovered (repr): {recovered!r}\n"
        f"  patched php:\n{patched}"
    )
    print(f"  PASS: {name}")


# ---------------------------------------------------------------------------
# Tests
# ---------------------------------------------------------------------------

def test_plain_single_quote():
    run_test("plain single-quoted", "'oldpassword'", "newpassword")

def test_plain_double_quote():
    run_test("plain double-quoted", '"oldpassword"', "newpassword")

def test_apostrophe():
    run_test("apostrophe in password", "'oldpass'", "it's'me")

def test_single_backslash():
    run_test("single backslash", "'oldpass'", "pass\\word")

def test_consecutive_backslashes():
    run_test("consecutive backslashes", "'oldpass'", "pass\\\\double")

def test_trailing_backslash():
    run_test("trailing backslash", "'oldpass'", "password\\")

def test_backslash_before_quote():
    run_test("backslash before apostrophe", "'oldpass'", "abc\\'def")

def test_multiple_special_chars():
    run_test("mix: backslash + apostrophe + trailing backslash",
             '"oldpass"', "p\\'as's\\wo\\rd\\")

def test_empty_original():
    run_test("empty original password", "''", "newpass123")

def test_no_change_needed():
    # Count should be 1 even if old == new (pattern matches)
    php = make_php("'same'")
    patched, count = patch_pass(php, "same")
    assert count == 1, f"expected 1, got {count}"
    recovered = extract_pass(patched)
    assert recovered == "same", f"recovered: {recovered!r}"
    print("  PASS: no-change-needed (count still 1)")

def test_not_found_returns_zero():
    php = "<?php\n// no $this->pass here\n"
    _, count = patch_pass(php, "whatever")
    assert count == 0, f"expected 0, got {count}"
    print("  PASS: not-found returns 0")

def test_double_assignment_returns_two():
    php = make_php("'first'") + "\n        $this->pass = 'second';\n"
    _, count = patch_pass(php, "new")
    assert count == 2, f"expected 2, got {count}"
    print("  PASS: double-assignment returns 2")


if __name__ == "__main__":
    tests = [
        test_plain_single_quote,
        test_plain_double_quote,
        test_apostrophe,
        test_single_backslash,
        test_consecutive_backslashes,
        test_trailing_backslash,
        test_backslash_before_quote,
        test_multiple_special_chars,
        test_empty_original,
        test_no_change_needed,
        test_not_found_returns_zero,
        test_double_assignment_returns_two,
    ]
    print(f"Running {len(tests)} tests...")
    failures = 0
    for t in tests:
        try:
            t()
        except AssertionError as e:
            print(f"  FAIL: {t.__name__}\n    {e}")
            failures += 1
        except Exception as e:
            print(f"  ERROR: {t.__name__}: {e}")
            failures += 1
    print()
    if failures:
        print(f"FAILED: {failures}/{len(tests)} tests failed")
        sys.exit(1)
    else:
        print(f"ALL {len(tests)} TESTS PASSED")
