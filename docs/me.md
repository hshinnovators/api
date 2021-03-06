# KentProjects > Me

An endpoint to get information about the currently-logged-in user.

```http
GET /me HTTP/1.1
```

```json
{
    "user": {
        "id": 3,
        "email": "mh471@kent.ac.uk",
        "name": "Matt House",
        "first_name": "Matt",
        "last_name": "House",
        "role": "student",
        "bio": null,
        "permissions": {
            "create": 0,
            "read": 0,
            "update": 0,
            "delete": 0
        },
        "created": "2014-11-21 21:31:52",
        "lastlogin": "2014-01-01 00:00:00",
        "updated": "2014-12-16 16:47:06"
    },
    "group": {
        "id": 1,
        "year": "2014",
        "name": "The Master Commanders",
        "students": [
            {
                "id": 3,
                "email": "mh471@kent.ac.uk",
                "name": "Matt House",
                "first_name": "Matt",
                "last_name": "House",
                "role": "student",
                "bio": null,
                "permissions": {
                    "create": 0,
                    "read": 0,
                    "update": 0,
                    "delete": 0
                },
                "created": "2014-11-21 21:31:52",
                "lastlogin": "2014-01-01 00:00:00",
                "updated": "2014-12-16 16:47:06"
            },
            {
                "id": 5,
                "email": "mjw59@kent.ac.uk",
                "name": "Matthew Weeks",
                "first_name": "Matthew",
                "last_name": "Weeks",
                "role": "student",
                "bio": null,
                "permissions": {
                    "create": 0,
                    "read": 0,
                    "update": 0,
                    "delete": 0
                },
                "created": "2014-11-27 20:12:15",
                "lastlogin": "2014-01-01 00:00:00",
                "updated": "2014-12-16 16:47:09"
            }
        ],
        "creator": {
            "id": 3,
            "email": "mh471@kent.ac.uk",
            "name": "Matt House",
            "first_name": "Matt",
            "last_name": "House",
            "role": "student",
            "bio": null,
            "permissions": {
                "create": 0,
                "read": 0,
                "update": 0,
                "delete": 0
            },
            "created": "2014-11-21 21:31:52",
            "lastlogin": "2014-01-01 00:00:00",
            "updated": "2014-12-16 16:47:06"
        },
        "permissions": {
            "create": 0,
            "read": 0,
            "update": 0,
            "delete": 0
        },
        "created": "2015-02-09 11:02:31",
        "updated": "2015-02-09 11:02:31"
    },
    "project": {
        "id": 1,
        "year": "2014",
        "group": 1,
        "name": "Student Project Support System",
        "slug": "student-project-support-system",
        "description": "![Good morning Charlie](https://flavorwire.files.wordpress.com/2010/11/charlie_angel.jpg)\n\nGood morning Angels,\n\nThis is a project that can only be undertaken by 3 of the most devilishly hansdsome, career-wreckingly clever and world-endingly stupid students in the school of computing.\n\nYour mission, should you choose to accept it, is to build a replacement for the f***m that has been used in previous years to help students choose a project, where they can quickly and easily form groups with their fellow socially inept colleagues.\n\n![The Mighty JC](http://i.imgur.com/ldS4dWw.png \"enter image title here\")",
        "creator": {
            "id": 2,
            "email": "J.S.Crawford@kent.ac.uk",
            "name": "John Crawford",
            "first_name": "John",
            "last_name": "Crawford",
            "role": "staff",
            "bio": "Anything.",
            "permissions": {
                "create": 0,
                "read": 0,
                "update": 0,
                "delete": 0
            },
            "created": "2014-11-21 21:31:46",
            "lastlogin": "2014-01-01 00:00:00",
            "updated": "2014-12-16 16:47:03"
        },
        "permissions": {
            "create": 0,
            "read": 0,
            "update": 0,
            "delete": 0
        },
        "created": "2015-02-09 11:02:31",
        "updated": "2015-02-13 13:04:23"
    },
    "notifications": [
        {
            "id": 3,
            "type": "user_got_a_notification",
            "text": "You just got a notification. Yippee!",
            "actor": {
                "id": 4,
                "uid": "jsd24",
                "email": "jsd24@kent.ac.uk",
                "name": "James Dryden",
                "first_name": "James",
                "last_name": "Dryden",
                "role": "student",
                "bio": null,
                "interests": [

                ],
                "permissions": {
                    "create": false,
                    "read": true,
                    "update": true,
                    "delete": false
                },
                "created": "2014-11-27 19:19:35",
                "last_login": "2014-01-01 00:00:00",
                "updated": "2015-03-06 10:14:07"
            },
            "group": null,
            "project": null,
            "user": null,
            "year": "",
            "created": "2015-03-06 13:05:26",
            "read": null
        }
    ],
    "settings": [

    ]
}
```

----

## Get a user's notifications

When a notification is generated, we want to get to the user as soon as possible.

Therefore, we can check for new notifications like so:

```http
GET /me/notifications HTTP/1.1
```

This will return a JSON array of notifications, as well as some useful headers too:

```http
HTTP/1.1 200 OK
X-Notification-Count: 1
```

```json
[
    {
        "id": 3,
        "type": "user_got_a_notification",
        "text": "You just got a notification. Yippee!",
        "actor": {
            "id": 4,
            "uid": "jsd24",
            "email": "jsd24@kent.ac.uk",
            "name": "James Dryden",
            "first_name": "James",
            "last_name": "Dryden",
            "role": "student",
            "bio": null,
            "interests": [

            ],
            "permissions": {
                "create": false,
                "read": true,
                "update": true,
                "delete": false
            },
            "created": "2014-11-27 19:19:35",
            "last_login": "2014-01-01 00:00:00",
            "updated": "2015-03-06 10:14:07"
        },
        "group": null,
        "project": null,
        "user": null,
        "year": "",
        "created": "2015-03-06 13:05:26",
        "read": null
    }
]
```

The header `X-Notification-Count` contains a count of the notifications you should have received.

### Only get unread notifications

```http
GET /me/notifications?unread=1 HTTP/1.1
```

This will return a very similar JSON array of notifications, as well as some useful headers too:

```http
HTTP/1.1 200 OK
X-Notification-Count: 1
```

```json
[
    {
        "id": 3,
        "type": "user_got_a_notification",
        "text": "You just got a notification. Yippee!",
        "actor": {
            "id": 4,
            "uid": "jsd24",
            "email": "jsd24@kent.ac.uk",
            "name": "James Dryden",
            "first_name": "James",
            "last_name": "Dryden",
            "role": "student",
            "bio": null,
            "interests": [

            ],
            "permissions": {
                "create": false,
                "read": true,
                "update": true,
                "delete": false
            },
            "created": "2014-11-27 19:19:35",
            "last_login": "2014-01-01 00:00:00",
            "updated": "2015-03-06 10:14:07"
        },
        "group": null,
        "project": null,
        "user": null,
        "year": "",
        "created": "2015-03-06 13:05:26",
        "read": null
    }
]
```

Again, the header `X-Notification-Count` contains a count of the notifications you should have received. If you want the
unread notifications request to be quicker you should make a `HEAD` request and just check this header! (Yes, `HEAD`
requests are totally viable with this platform!)