# CoinnessAPI

CoinnessAPI 是用于调用  Coinness.com 提供相关接口的PHP端程序包。使用此接口，需要先联系 Coinness.com 申请 APP_ID 和 APP_SERCET。

## 接口
1. getNewsflashList
获取快讯列表，请求参数包括：
language : 快讯的语言，可以使用 COINNESS_LANGUAGE_* 指定
start_time ： 最早的快讯的时间，可以不指定，表示不限制最早快讯的时间；
end_time : 最晚的快讯的时间，可以不指定，默认表示当前时间；
size : 每次返回的快讯条数，默认150，最大支持500；

返回值
 * 所有请求参数都将返回
 count :  符合条件的记录数，如果超过 size 限制，表示本次未全部返回（ 如果未提供 start_time ，此参数将永远为 0）
 items :  newsflash 组成的数组
 // 每个newsflash 包含：
    id  	  int	        Yes	快讯的编号
    title	  string    	No	快讯标题（如果未删除的快讯，内容和标题，至少会有一个）
    content	string  	  No	快讯内容（如果未删除的快讯，内容和标题，至少会有一个）
    link  	string	    No	快讯相关内容的延伸阅读地址
    link_title	string	No	快讯相关内容的延伸阅读地址的链接标题（如果 link为空时候，请忽略此参数）
    issue_time	int	    Yes	快讯发布时间（展示给最终用户的）
    update_time	int   	Yes	快讯最后修改时间
    deleted	0/1	Yes	是否已经删除，0：未删除；1：删除（注意：如果已删除，不会返回 title 和 content）

2. getNewsflashUpdated
获取最近8小时有修改或者删除的快讯（如果发布后未修改的，不会返回）
请求参数只有 language : 快讯的语言，可以使用 COINNESS_LANGUAGE_* 指定

返回值：
 * 所有请求参数都将返回
 count	int	符合要求的记录数量（ 如果未提供 start_time ，此参数将永远为 0）
 items :  newsflash 组成的数组（同上）
 
错误码：

1001	签名验证失败	sign 签名验证失败
1002	请求超时	请求时间timestamp 与当前系统时间相差超过10秒（大于或者小于数超过10秒）
1003	不支持的语言标识	当请求的语言 language 不在支持的语言范围的时候，报这个错误
