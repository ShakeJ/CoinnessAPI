# CoinnessAPI
CoinnessAPI 是用于调用  Coinness.com 提供相关接口的PHP端程序包。使用此接口，需要先联系 Coinness.com 申请 APP_ID 和 APP_SERCET。
## 调用须知
1. 定时轮询的方式（每个10秒），调用 getNewsflashList 方法，获取最新的快讯，只需要传递 language 和 start_time 即可，start_time 可以使用上次请求到的快讯的发布时间；
2. 定时轮询的方式（建议每隔30秒）调用 getNewsflashUpdated 方法，获取发布后发生修改的快讯，从而保证快讯内容与 coinness 同步；
## 接口 interface
### getNewsflashList
获取快讯列表

#### 请求参数
| 参数 | 类型 | 是否必须 | 默认值 | 说明 |
|-----|------|--------|-------|------|
| language | string | 是 | | 快讯的语言，可以使用 COINNESS_LANGUAGE_* 指定	|
| start_time | int | 否 | 0 |最早的快讯的时间，可以不指定，表示不限制最早快讯的时间|
| end_time | int | 否 | 0 | 最晚的快讯的时间，可以不指定，默认表示当前时间 |
| size | int | 否 | 150 | 每次返回的快讯条数，默认150，最大支持500 |
说明：
1.  start_time 和 end_time 都是以发布时间(issue_time)为准；  
2.  返回的内容，按照 issu_time 倒序进行排序；
3.  start_time 和 end_time 的不同组合的处理方式：
3.1.  start_time 和 end_time 均为空的时候，返回当前时间之前的快讯，最多返回 size 条，返回值不包含 count;
3.2.  只提供 start_time ，表示获取 start_time 之后到当前时间之前的快讯，返回最多 size 条，返回值包含 count 表示总共有多少条快讯；
3.3.  只提供 end_time，表示获取 end_time 之间之前的快讯，最多返回 size 条，返回值不包含 count
3.4.  同时指定 start_time 和 end_time ，返回 start_time 和 end_time 之间的快讯，最多返回 size 条，返回值会包含 count ，表示总共有多少条快讯；
4.  指定了 start_time 和 end_time 的时候，如果包含的记录数量超过 size 返回，将不会返回。这时可以通过 count 判断是否有更多的，然后修改请求参数获取剩余部分；

#### 返回值
| 名称 | 类型 |	说明	|
|-----|----|----|
| language | string | 所请求快讯的语言（来自请求参数） |
| start_time | int | 请求的快讯的开始时间（来自请求参数） | 
| end_time | int | 所请求的快讯的截止时间（来自请求参数，如果没设置，将使用当前时间） |
| size | int | 一次需要返回的数量（来自请求参数，但是如果超过范围会被自动修改）|
| count | int | 符合要求的记录数量（ 如果未提供 start_time ，此参数将永远为 0） |
| items | array of newsflash | 快讯记录（如果指定的条件找不到合适的快讯内容，会返回空的 array ，但是不会报错）|

每个newsflash 包含：
| 名称 | 类型 | 说明 |
|----|----|----|
| id | int | 快讯的编号 |
| title | string | 快讯标题（如果未删除的快讯，内容和标题，至少会有一个） |
| content | string | 快讯内容（如果未删除的快讯，内容和标题，至少会有一个）|
| link | string | 快讯相关内容的延伸阅读地址 | 
| link_title | string | 快讯相关内容的延伸阅读地址的链接标题（如果 link为空时候，请忽略此参数）|
| issue_time | int | 快讯发布时间（展示给最终用户的）|
| update_time | int | 快讯最后修改时间 | 
| deleted | 0/1 | 是否已经删除，0：未删除；1：删除；注意：如果已删除，不会返回 title 和 content | 

### getNewsflashUpdated
获取最近8小时有修改或者删除的快讯（如果发布后未修改的，不会返回）
#### 请求参数
| 参数 | 类型 | 是否必须 | 默认值 | 说明 |
|-----|------|--------|-------|------|
| language | string | 是 |  | 快讯的语言，可以使用 COINNESS_LANGUAGE_* 指定	|

#### 返回值
| 名称 | 类型 |	说明	|
|-----|----|----|
| language | string | 所请求快讯的语言（来自请求参数） |
| count | int | 符合要求的记录数量（ 如果未提供 start_time ，此参数将永远为 0） |
| items | array of newsflash | 快讯记录（如果指定的条件找不到合适的快讯内容，会返回空的 array ，但是不会报错）|
 
### 错误码：
| 错误码 | 错误信息 | 说明 |
|----|----|----|
| 1001 | 签名验证失败 | sign 签名验证失败 |
| 1002 | 请求超时 | 请求时间timestamp 与当前系统时间相差超过10秒（大于或者小于数超过10秒）|
| 1003 | 不支持的语言标识 | 当请求的语言 language 不在支持的语言范围的时候，报这个错误 |
