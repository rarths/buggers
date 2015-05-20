<?php
$app->router->add('setup', function() use ($app) 
{
    $app->db->setVerbose(false);  // Set verbose mode
 
    $app->db->dropTableIfExists('user')->execute();
    $app->db->createTable(
        'user',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'acronym' => ['varchar(20)', 'unique', 'not null'],
            'email' => ['varchar(80)'],
            'name' => ['varchar(80)'],
            'password' => ['varchar(255)'],
            'created' => ['datetime'],
            'updated' => ['datetime'],
            'deleted' => ['datetime'],
            'active' => ['datetime'],
        ]
    )->execute();

    $app->db->insert(
            'user',
            ['acronym', 'email', 'name', 'password', 'created', 'active']
        );
     
    $now = gmdate('Y-m-d H:i:s');
 
    $app->db->execute([
        'admin',
        'admin@dbwebb.se',
        'Administrator',
        password_hash('admin', PASSWORD_DEFAULT),
        $now,
        $now
    ]);
 
    $app->db->execute([
        'doe',
        'doe@dbwebb.se',
        'John/Jane Doe',
        password_hash('doe', PASSWORD_DEFAULT),
        $now,
        $now
    ]);

    $app->db->execute([
        'nintendo',
        'nino@dbwebb.se',
        'Hiroshi Miyamoto',
        password_hash('doe', PASSWORD_DEFAULT),
        $now,
        $now
    ]);

    $app->db->execute([
        'HB',
        'r3dn3ck@dbwebb.se',
        'Hillbilly Baggins',
        password_hash('doe', PASSWORD_DEFAULT),
        $now,
        $now
    ]);


    // USERVIEW
    $app->db->dropTableIfExists('userview')->execute();
    $sql = "
        CREATE VIEW prj_userview AS
        SELECT prj_user.id, prj_user.name, prj_user.acronym, prj_user.email, prj_user.created
        FROM prj_user
        WHERE prj_user.active IS NOT NULL AND prj_user.deleted IS NULL
    ";
    $app->db->execute($sql,[]);

    // REPORTS
    $app->db->dropTableIfExists('report')->execute();
    $app->db->createTable(
        'report',
        [
            'id'        => ['integer', 'primary key', 'not null', 'auto_increment'],
            'userid'    => ['integer', 'not null'],
            'voteid'    => ['integer'],
            'solvedby'  => ['integer'],
            'content'   => ['text'],
            'title'     => ['varchar(255)', 'not null'],
            'created'   => ['datetime', 'not null'],
        ]
    )->execute();


    // EXTEND
    $app->db->dropTableIfExists('extend')->execute();
    $app->db->createTable(
        'extend',
        [
            'id'        => ['integer', 'primary key', 'not null', 'auto_increment'],
            'userid'    => ['integer', 'not null'],
            'voteid'    => ['integer'],
            'reportid'  => ['integer', 'not null'],
            'content'   => ['text'],
            'title'     => ['varchar(255)', 'not null'],
            'created'   => ['datetime', 'not null'],
        ]
    )->execute();  


    // COMMENTS
    $app->db->dropTableIfExists('comment')->execute();
    $app->db->createTable(
        'comment',
        [
            'id'        => ['integer', 'primary key', 'not null', 'auto_increment'],
            'userid'    => ['integer', 'not null'],
            'typeid'    => ['integer', 'not null'],
            'content'   => ['text'],
            'type'      => ['varchar(80)'],
            'created'   => ['datetime'],
        ]
    )->execute();


    // TAG
    $app->db->dropTableIfExists('tag')->execute();
    $app->db->createTable(
        'tag',
        [
            'id'    => ['integer', 'primary key', 'not null', 'auto_increment'],
            'name'  => ['varchar(255)'],

        ]
    )->execute();


    // TAG REPORT
    $app->db->dropTableIfExists('tagreport')->execute();
    $app->db->createTable(
        'tagreport',
        [
            'id'        => ['integer', 'primary key', 'not null', 'auto_increment'],
            'tagid'     => ['integer', 'not null'],
            'reportid'  => ['integer', 'not null'],
        ]
    )->execute();


    // VOTE REPORT
    $app->db->dropTableIfExists('vote')->execute();
    $app->db->createTable(
        'vote',
        [
            'id'            => ['integer', 'primary key', 'not null', 'auto_increment'],
            'userid'        => ['integer', 'not null'],
            'posttypeid'    => ['integer', 'not null'],
            'vote'          => ['varchar(1)'],
            'posttype'      => ['varchar(80)'],
            'created'       => ['datetime'],
        ]
    )->execute(); 

    $app->theme->setTitle("DB RESET");
    $app->views->add('default/page', [
        'title'     => 'Database Reset',
        'content'   => 'Enjoy your fresh new database!'
    ]);  
});