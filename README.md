# Youkok2

[![Build Status](https://travis-ci.org/OptimusCrime/youkok2.svg?branch=master)](https://travis-ci.org/OptimusCrime/youkok2)
[![Coverage Status](https://coveralls.io/repos/github/OptimusCrime/youkok2/badge.svg?branch=master)](https://coveralls.io/github/OptimusCrime/youkok2)

Youkok2 is available at [youkok2.com](http://youkok2.com).

## Run cron jobs

Add the following to the host machine crontab:

**For dev:**

```
30 1 * * * docker-compose run --rm server cron_job 
```

**For production:**

```
30 1 * * * docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm server cron_job 
```
