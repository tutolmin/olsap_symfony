-- Adminer 4.8.1 PostgreSQL 13.7 dump

INSERT INTO "breeds" ("id", "name") VALUES
(1,	'Ubuntu'),
(2,	'Fedora'),
(3,	'RHEL');

INSERT INTO "domains" ("id", "name", "description") VALUES
(0,	'Storage',	'Everything related to storage subsystem, including volume managers, working with block devices, mounting, formatting, etc.'),
(1,	'Security',	'SELinux, passwords, kerberos, openssl, file permissions, ACLs, etc.'),
(2,	'Network',	'Tasks related to network configuration, monitoring and troubleshooting.'),
(5,	'Automation',	'Collection of tasks related to automating repeating work.'),
(6,	'Performance',	'Tuning system parameters'),
(9,	'System management',	'systemd, processes'),
(10,	'Virtualization',	'docker, LXC'),
(8,	'Hardware',	'lspci, dmidecode'),
(7,	'Software',	'yum, dnf, rpm'),
(11,	'Monitoring',	'get system running status and metrics');

INSERT INTO "environment_statuses" ("id", "status", "description") VALUES
(2,	'Created',	NULL),
(5,	'Complete',	NULL),
(6,	'Verified',	NULL),
(7,	'Solved',	NULL),
(1,	'New',	'New Environment entity not linked to Session or Instances'),
(4,	'Skipped',	'User clicked Skipped button during test Session'),
(3,	'Deployed',	'Deployment playbook has been run for the Environment');

INSERT INTO "hardware_profiles" ("id", "type", "description", "cost", "name", "supported") VALUES
(3,	'1',	'VM with 1 CPU and 1G memory',	1500,	'baseball',	'0'),
(0,	'0',	'Container, 10% CPU allowance, 256MB memory, no swap, 1GB root',	10,	'cricket',	'1'),
(2,	'1',	'VM with 1 CPU and 512M memory',	1000,	'tennis',	'0'),
(1,	'0',	'Container, 10% CPU allowance, 256MB memory, 128MB swap, 1GB root',	20,	'soccer',	'1');

INSERT INTO "instance_statuses" ("id", "status", "description") VALUES
(2,	'Bound',	NULL),
(8,	'Sleeping',	'Stopped LXC instance bound to an active environment'),
(5,	'Started',	'Unbound started LXC instance, ready for allocation'),
(6,	'New',	'New Instance entity not linked to an actual LXC instance'),
(7,	'Running',	'Started LXC instance bound to an active Environment'),
(4,	'Stopped',	'Unbound stopped LXC instance, ready for allocation');

INSERT INTO "operating_systems" ("id", "release", "description", "supported", "breed_id", "alias") VALUES
(2,	'18.04 LTS',	'Major version',	'1',	1,	'bionic'),
(5,	'33',	'Older version',	'0',	2,	'f33'),
(4,	'35',	'New version',	'0',	2,	'f35'),
(3,	'22.04 LTS',	'Modern version',	'0',	1,	'jammy'),
(7,	'20.04 LTS',	'Current release',	'1',	1,	'focal');

INSERT INTO "task_oses" ("id", "task_id", "os_id") VALUES
(17,	1,	2),
(18,	1,	7),
(19,	2,	2),
(20,	2,	7),
(21,	7,	2),
(22,	7,	7),
(25,	5,	2),
(26,	5,	7),
(27,	6,	2),
(28,	6,	7),
(29,	8,	2),
(30,	8,	7),
(31,	9,	2),
(32,	9,	7),
(33,	10,	2),
(34,	10,	7),
(35,	11,	2),
(36,	11,	7),
(37,	12,	2),
(38,	12,	7),
(39,	13,	2),
(40,	13,	7),
(41,	14,	2),
(42,	14,	7),
(43,	16,	2),
(44,	16,	7),
(45,	15,	2),
(46,	15,	7),
(47,	17,	2),
(48,	17,	7);

INSERT INTO "task_techs" ("id", "task_id", "tech_id") VALUES
(1,	1,	14),
(2,	2,	14),
(5,	5,	6),
(6,	6,	22),
(7,	5,	5),
(8,	7,	23),
(9,	8,	22),
(10,	9,	14),
(11,	10,	14),
(12,	11,	14),
(13,	12,	14),
(14,	13,	24),
(15,	14,	25),
(16,	15,	14),
(17,	15,	6),
(18,	16,	14),
(19,	17,	14),
(20,	16,	6),
(21,	17,	6);

INSERT INTO "tasks" ("id", "name", "description", "path", "project", "solve", "deploy", "verify") VALUES
(13,	'Get default gateway',	'Get system default gateway and store the value into the file: /var/tmp/default_gateway',	'default_gateway',	72,	74,	73,	75),
(14,	'Extract TGZ archive',	'Extract tar.1.gz file from archive /var/tmp/archive.tgz and put it into /var/tmp',	'extract_tgz_file',	76,	78,	77,	79),
(15,	'Create a file and make it readable by user only',	'Create a file ''test_file'' in /var/tmp and remove read permission for group and others',	'unset_file_read_perm',	80,	82,	81,	83),
(17,	'Create a file and make it writable',	'Create a file ''test_file'' in /var/tmp and set write permission for owner, group and others',	'set_file_write_perm',	88,	90,	89,	91),
(16,	'Create a file and make it executable',	'Create a file ''test_file'' in /var/tmp and set execute permission',	'set_file_execute_perm',	84,	86,	85,	87),
(8,	'Install deb package',	'Install ''nano'' package into the OS',	'deb_install',	40,	42,	41,	43),
(6,	'Uninstall deb package',	'uninstall ''nano'' package from the OS',	'deb_uninstall',	36,	38,	37,	39),
(1,	'Create a directory',	'Create a directory: /var/tmp/test_dir',	'create_directory',	48,	50,	49,	51),
(2,	'Create a file',	'Create a file: /var/tmp/test_file',	'create_file',	44,	46,	45,	47),
(7,	'Get system UUID',	'Fetch system universally unique identifier and store it in the file: /var/tmp/uuid.txt',	'system_uuid',	28,	30,	29,	31),
(5,	'Set immune flag',	'Set immune flag to a file in the following location /var/tmp/test_file',	'set_immune',	32,	34,	33,	35),
(11,	'Create a hard link',	'Create a file: /var/tmp/test_file and a hard link to it /var/tmp/hard_link Then store their inode number in the file',	'create_hardlink',	64,	66,	65,	67),
(10,	'Create a named pipe',	'Create a named pipe in /var/tmp called test_fifo',	'create_fifo',	60,	62,	61,	63),
(9,	'Create a symbolic link',	'Create a symbolic link to /etc/hosts in /var/tmp',	'create_symlink',	56,	58,	57,	59),
(12,	'Create parent directories',	'Please create a following directory structure: /var/tmp/3/1/4/1/5/9/2/6/5/3/5/8/9/7/9/3/2/3/8/4/6/2/6/4/3/3/8/3/2/7/9/5/0/2/8/8/4/1/9/7/1/6/9/3/9/9/3/7/5/1/0/5/8/2/0/9/7/4/9/4/4/5/9/2',	'create_parent_directory',	68,	70,	69,	71);

INSERT INTO "technologies" ("id", "domain_id", "name", "description") VALUES
(1,	0,	'LVM',	'Logical Volume Manager configuration, managing and troubleshooting.'),
(0,	0,	'Ceph',	'Configuring and troubleshooting Ceph storage.'),
(2,	2,	'Netplan',	'Configuring the network with netplan.'),
(3,	2,	'NetworkManager',	'Configuring the network with NetworkManager.'),
(4,	2,	'Bonding',	'Configure interface bonding.'),
(5,	1,	'ACL',	'Setting and removing file ACLs'),
(6,	1,	'Permissions',	'Changing file permissions on filesystems.'),
(7,	5,	'Ansible',	'Configuration management.'),
(8,	6,	'Tuned',	'Special profiles for system parameters'),
(9,	0,	'Filesystems',	'creating, resizing, repairing'),
(10,	7,	'RPM',	NULL),
(11,	7,	'DNF',	NULL),
(12,	10,	'docker',	NULL),
(13,	7,	'APT',	NULL),
(14,	9,	'Files and directories',	NULL),
(15,	0,	'NFS',	NULL),
(16,	9,	'Systemd',	NULL),
(17,	2,	'Firewall',	NULL),
(18,	9,	'Remote access',	'Access via SSH'),
(20,	11,	'Network traffic',	'capture, analyze network traffic.'),
(19,	11,	'View processes',	'top, nmon, etc.'),
(21,	11,	'I/O activity',	'view I/O activity, iostat'),
(22,	7,	'Dpkg',	'use debian package manager'),
(23,	8,	'dmidecode',	'dmidecode  is a tool for dumping a computer''s DMI (some say SMBIOS) table contents in a human-readable format.'),
(24,	2,	'Network Settings',	'Network subsystem settings'),
(25,	9,	'Archiving',	'ZIP, GZip, TAR, etc.'),
(26,	9,	'Users and groups',	'Adding, removing, managing users and groups');

INSERT INTO "testees" ("id", "email", "oauth_token", "registered_at") VALUES
(1,	'tutolmin@gmail.com',	'slakdjfsaofasldjfowijfasldkfjo45',	'2021-06-20 09:25:00'),
(2,	'fercerpav@gmail.com',	'gsdfgsdghhwsehegsdfgsdfg',	'2022-10-10 12:40:00'),
(3,	'strakhov.oleg@gmail.com',	'ghghghhjrtytsdsdfgsdfgs',	'2022-10-10 15:00:00');

-- 2022-10-28 13:09:08.627851+00
