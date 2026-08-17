{pkgs}: {
  deps = [
    pkgs.eas-cli
    pkgs.chromium
    pkgs.php82Extensions.pdo_mysql
    pkgs.php82Extensions.mysqli
    pkgs.mysql80
  ];
}
