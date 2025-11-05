Apache configuration notes for PCOFlow

1) Allow public PWA files without symlink errors

If you deploy using symlinks for files under `public/`, enable one of the following on your VirtualHost or directory:

```
<Directory /var/www/html/public>
    Options +SymLinksIfOwnerMatch
    # or: Options +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Prefer creating real files (no symlinks) for `web-manifest.json` and `web-sw.js` in `public/`.

2) Ensure correct MIME types

```
AddType application/manifest+json .webmanifest .web-manifest .json
AddType application/javascript .js
```

3) Cache-friendly headers (optional)

```
<FilesMatch "\.(json|js)$">
    Header set Cache-Control "public, max-age=3600"
</FilesMatch>
```


