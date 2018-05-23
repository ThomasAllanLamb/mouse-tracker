# SETUP

In `code/site.conf`, set `server_name` to the domain you want to use. Suppose `mouse-tracker.local`.

In `docker-compose.yml`, set the first number of the `web` container's `port`. Suppose `8080:80` to have it show up at mouse-tracker.local:8080.

In `/etc/hosts`:
```
127.0.0.1 mouse-tracker.local
```

In a terminal
```bash
docker-compose up
```

visit [http://mouse-tracker.local:8080]

# TODO
- Secure mysql
- Consider removing router
- Are all /images still being used?
- Border of screen is not handled properly
- Add an ID key instead of letting users pick their own