<?php
$repo_dir       = '/full/path/to/bare/repo.git';
$web_root_dir   = '/full/path/to/web/root';
$branch         = 'branch_name';

// Full path to git binary is required if git is not in your PHP user's path. Otherwise just use 'git'.
$git_bin_path = '/usr/bin/git';

$update = false;

// Parse data from Bitbucket hook payload
$payload = json_decode(file_get_contents("php://input"));

$push = $payload->push;

if (!empty($push) && !empty($push->changes[0])) {
    $new = $push->changes[0]->new;
    if (!empty($new)) {
        if ($new->name === $branch) {
            // Do a git checkout to the web root
            shell_exec('cd ' . $repo_dir . ' && ' . $git_bin_path  . ' fetch');
            shell_exec('cd ' . $repo_dir . ' && GIT_WORK_TREE=' . $web_root_dir . ' ' . $git_bin_path  . ' checkout -f');

            // Log the deployment
            $commit_hash = $new->target->hash;

            file_put_contents('deploy.log', date('m/d/Y h:i:s a') . " Deployed branch: " .  $branch . " Commit: " . $commit_hash . "\n", FILE_APPEND);
        }
    }
}

