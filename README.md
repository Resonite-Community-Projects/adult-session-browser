# Resonite Restricted Session System

Enables browsing and viewing available adult sessions within Resonite communities through a Resonite dash facet as well as other endpoints. Being an open data server, arbitrary display methods can be created in Resonite as well as other platforms such as a Discord bot.

This is one piece of the overall Resonite adult session access system. As this is not native Resonite functionality, this aims to add the ability to easily host access-restricted sessions without requiring client or server mods/plugins. This project utilizes 100% native-Resonite functionality including web POST/GET, and significant UIX manipulation with ProtoFlux.

The following are details on the overall system and architecture:

## Cloud Variable Access Manager

To be in alignment with Resonite usage guidelines, to host age-restricted sessions (18+) the session must be access controlled. There are multiple ways to perform this task, private invite-only, contacts only, and cloud variables. To support large dynamic communities with multiple headless server accounts, cloud variables is the preferred method. This adds flexibility, however creates a level of difficulty in managing the cloud variable owner values for each allowed user.

First it is important to understand cloud variables, notably the difference between variable definitions, and variable owners.
ProbablePrime has excellent videos on these items: https://www.youtube.com/watch?v=beO3UNaLhQ0

### Setting up cloud vars

*Some users are affected by a Resonite bug in which accounts migrated from Neos are unable to read from cloud vars:*
* https://github.com/Yellow-Dog-Man/Resonite-Issues/issues/3101
* https://github.com/Yellow-Dog-Man/Resonite-Issues/issues/3133
* https://github.com/Yellow-Dog-Man/Resonite-Issues/issues/537

There is no fix for this issue at the moment - you will have to manually override the access key within the updater itself.
1. Inspect the updater, and open the "cloud var" child.
2. Attach Component, Data -> ValueField<string>.
3. Paste your access token in the Value field (blue highlight).
4. Grab the Value field reference (green highlight) and drag it into the DynamicField Target (red highlight).

<img width="761" height="1180" alt="image" src="https://github.com/user-attachments/assets/9f4cf587-36f6-40d1-95ca-0d67cd5e5bba" />

First step is to create a Resonite group: https://wiki.resonite.com/Groups

An admin of the Resonite group can run these commands to the Resonite Bot to create the required cloud variable for access control:

```
/creategroupvar "My Resonite Group" sessionAccess
/setgroupvarperms "My Resonite Group" sessionAccess read,write definition_owner
/setgroupvartype "My Resonite Group" sessionAccess bool
/setgroupvardefaultvalue "My Resonite Group" sessionAccess false
```

These commands will create the variable, configure the correct permissions, and finally set the default value to disallow all players who have not been explicitly allowed.

### Allowing individual user access

Once the variable is created, updating individual user values is best handled through the Discord bot integration. This accesses the Resonite API directly externally to Resonite, and allows for updating user cloud variable values dynamically through Discord. https://github.com/Resonite-Community-Projects/accesslistmanager

## Accessing Hidden Sessions

Per Resonite guidelines, all adult sessions must be hidden from the main world browser and API. If you wish to use this project to run a special event, such as an event which requires a registration fee, this could be used to only allow VIPs or similar.

As adult sessions must be hidden, it is not possible to get an invite/join link, view friends/contacts in the session, user count, etc. This system provides a 3rd party external API for these sessions. Usage of this system to access hidden adult events without an authentication method in place (cloud variables) is NOT allowed per Resonite guidelines. This system MUST be deployed alongside the cloud variable system for adult session hosting.

The recommended session configuration is RegisteredUsers, hidden.

### Session update system

Within the session you wish to publish to the API and make available, a ProtoFlux bot is provided within this public folder:
`resrec:///U-GrayBoltWolf/R-72ff0dd2-b064-44b4-bfb4-2a338333f06d`

![image](https://user-images.githubusercontent.com/4554196/192861133-5d0f481c-bacd-4a7d-828d-fd173e92b766.png)

Placeholder help text is placed to give a general idea of the required settings, here is a completed build:

![image](https://user-images.githubusercontent.com/4554196/192861473-1ab52d16-032d-49fd-99c8-e6d5fd1ea93c.png)

Cloud vars are used to contain the access key to the API server. Cloud var is created with the following commands:

```
/creategroupvar "My Resonite Group" sessionUpdateKey
/setgroupvarperms "My Resonite Group" sessionUpdateKey write definition_owner
/setgroupvarperms "My Resonite Group" sessionUpdateKey read definition_owner_unsafe
/setgroupvartype "My Resonite Group" sessionUpdateKey string
/setgroupvarvalue "My Resonite Group" sessionUpdateKey U-SOME-BOT-ACCOUNT "ACCESSKEY"
```

These permissions only allow user accounts within your Resonite group to read the access key, meaning if the update system is taken from your session by a user, it will not function. The user could store your access key however, if that case arises then change your access key cloud var, which would automatically propogate to all session update systems spawned, removing the need to manually deploy a new access key if compromised. As Resonite does not support hashing/encryption within ProtoFlux, this is the best we can offer for now.

An existing API server is available at https://ad-sessions.resonite.boltwolf.net for most Resonite communities. ***If you wish to use my server, you will need to send me (GrayBoltWolf) your access key to allow you to push data to the server.***

If running from a headless server, ensure the update server is allowed in the headless config:

```  
    "allowedUrlHosts": [
        "localhost",
        "ad-sessions.resonite.boltwolf.net"
    ],
```

### Session access facet

Once session data is pushed to the server, data can be accessed and presented easily within a facet or in-world UIX. A facet is made available within this folder: `resrec:///U-GrayBoltWolf/R-5ceb026a-00d4-4b64-a922-dd34d33ba6d3`

A video is also provided for quick review of the Facet: https://youtu.be/dyGw4tEvZ_A

![image](https://user-images.githubusercontent.com/4554196/192862722-3de6dafb-9d29-4ed5-9a8f-b5ed41148f4d.png)

![image](https://user-images.githubusercontent.com/4554196/192862782-6463a985-06a2-403d-b185-1fc09dffcf43.png)

Various filter options are available, including the ability to only show events from specific communities:

![image](https://user-images.githubusercontent.com/4554196/192862928-f868eefd-c8c7-4f96-b4cf-3354eeeee883.png)

### Session ID handling recommendations

Within Resonite, session IDs must be unique. Because of this the data server uses the session ID as the unique identifier for each session. It is not required to set a static session ID within your headless server configuration, however in the event that the headless server crashes and reboots, a new session ID will be generated, and for a brief period of time the facet might display your session twice, one entry per unique session ID. It is recommended to set a static session ID so that in the event your headless crashes and reboots, the fresh session will have the same session ID and thus will simply update the existing entry on the facet.

Within the Resonite headless server configuration, this can be set on the line: `"customSessionId": "S-U-RESONITE-USER-ID:SESSIONNAME",`
Replace "RESONITE USER ID" with the user ID of the headless account, and "SESSIONNAME" with something unique, such as "party-time-1".
