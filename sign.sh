current_dir=$(dirname "$0")
# 执行签到
php $current_dir/bin/hyperf.php bilibili:clock_in

#crontab -e
#*/30 * * * * /data/project/biliHyperf/sign.sh
