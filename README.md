# bbk
fileformat: utf-8

bbk: 搜索比比看，是比较两个给定搜索引擎结果的盲测系统。
一个PHP程序，用于搜索引擎质量的盲测。
同时显示两个搜索引擎的结果，但是结果样式都是一样的，且不告诉用户谁是谁，让用户对结果进行选择。


PHP + MYSQL
PHP配置：
1. 纯php版：curl+iconv库支持
2. python版：打开disable_function:  pcntl_exec

MYSQL配置：
1.数据库创建脚本文件在sql/bbk.sql
2.数据库的配置文件在common/config.php


主要页面：
index.php~首页，显示投票排行榜
bbksearch.php~获取查询关键字并从搜索引擎中获取结果、
vote.php~对搜索引擎的结果相关性进行投票选择，并显示我的选择的历史投票数据。


基本流程
1.检索：输入关键字查询，从db中获取时间尚未过期的页面缓存（通过DB实现的时间阶段性缓存），如无结果，则下载页面。
2.显示历史投票数据：根据cookie名称来获取uid，再查询uid对应的历史投票数据。
