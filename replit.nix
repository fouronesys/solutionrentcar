{pkgs}: {
  deps = [
    pkgs.chromium
    pkgs.php82Extensions.pdo_mysql
    pkgs.php82Extensions.mysqli
    pkgs.mysql80
  ];
}
