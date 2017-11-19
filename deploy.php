<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'blog');

// Project repository
set('repository', 'https://github.com/chas-academy/05-dynamisk-webbplats-php-SebastianGerS.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
set('shared_files', ['config/dbinfo.json']);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);


// Hosts

host('ssh.binero.se')
    ->set('deploy_path', '~/sebastiangerstelsollerman.chas.academy')
    ->user('226728_sgs')
    ->port(22);    
    

// Tasks

desc('Deploy your project');
task('deploy:custom_webroot', function() {
    run("cd {{deploy_path}} && ln -sfn {{release_path}} public_html/05-dynamisk-webbplats-php-SebastianGerS");
});
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy', 'deploy:custom_webroot');

