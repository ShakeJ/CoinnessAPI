## Coinness API
Coinness API는 Coinness.com이 제공하는 관련 인터페이스를 호출하기 위해 사용되는 PHP 프로그램 패키지입니다. 이 인터페이스를 사용하려면 Coinness.com에 APP_ID와 APP_SERCET를 먼저 신청해야 합니다. 

### 호출 시 주의사항
1.	폴링 방법(10s/개)을 통한 getNewsflashList를 호출하여 뉴스피드를 받을 수 있다. 이 때  language와 start_time를 전달하면 된다. start_time은 앞서 요청한 뉴스피드 업로드시간으로 사용 가능하다.
2.	폴링 방법(30s 간격 권장)을 통한 getNewsflashUpdated를 호출하면 업로드 된 뉴스피드 중 수정된 내용을 받을 수 있다. 이를 통해 뉴스피드 내용을 coinness 와 동기화 할 수 있다.

### 포트 인터페이스
#### getNewsflashList
뉴스피드를 받을 수 있다.

##### 요청 파라미터
| 파라미터 | 유형 | 필수여부 | 디폴트값 | 설명 |
|----|----|----|----|----|
| language | string | 예	 | | 뉴스피드 언어 설정. COINNESS_LANGUAGE_* 를 사용하여 지정이 가능하다.|
| start_time | int | 아니오 | 0 | 가장 빨리 업로드 된 뉴스피드 시간. 지정하지 않아도 된다. 미지정 시 최근 뉴스피드 시간을 제한하지 않는다.|
| end_time | int | 아니오 | 0 | 가장 늦게 업로드 된 뉴스피드 시간. 지정하지 않다도 된다. 미지정 시 디폴트 값은 현재시간이다.|
| size | int | 아니오 | 150 | 매번 반환되는 뉴스피드 수. 디폴트 값은 150개이며, 최대 500개까지 가능하다.|

1.	start_time과 end_time은 모두 업로드 시간(issue_time)을 기준으로 한다.
2.	반환된 내용은 issu_time에 따라 역순으로 배열한다.
3.	start_time과 end_time의 다른 조합 처리방식:
3.1.	start_time과 end_time가 모두 0일 때, 현재시간 이전의 뉴스피드를 반환한다. 설정된 size 수까지 반환되며, 반환값은 count를 포함하지 않는다. 
3.2.	start_time만 제공 시, start_time 획득 후 현재 시간 이전의 뉴스피드를 표시한다. 설정된 size 수까지 반환된다. 반환값은 count를 포함하고 총 뉴스피드 수를 표시한다.
3.3.	end_time 만 제공 시, end_time을 획득하고 이전의 뉴스피드를 표시한다. 설정된 size 수까지 반환되며, 반환값은 count를 포함하지 않는다.
3.4.	start_time과 end_time 동시 설정 시, start_time과 end_time 이전의 뉴스피드를 반환한다. 설정된 size 수까지 반환된다. 반환값은 count를 포함하고 총 뉴스피드 수를 표시한다.
4.	start_time과 end_time을 지정 시, 만약 포함한 뉴스피드 수량이 설정된 size 반환을 초과하게 되면 반환되지 않는다. 이 때 count 를 통해 수량 초과여부를 판단할 수 있으며, 요청 파라미터를 수정하여 남은 부분을 얻을 수 있다.
#### 반환값
| 명칭 | 유형 | 설명 |
|----|----|----|
| language | string | 요청한 뉴스피드의 언어 (요청 파라미터에서 받음)|
| start_time | int | 요청한 뉴스피드의 시작시간 (요청 파라미터에서 받음)|
| end_time | int | 요청한 뉴스피드의 마감시간 (요청 파라미터에서 받음. 미 설정 시, 현재시간 사용) |
| size | int | 1회 반환 시 필요한 수량 (요청 파라미터에서 받음. 범위 초과 시 자동 수정 됨)|
| count | int | 조건에 맞는 기록 수량 (start_time 미 제공 시，해당 파라미터는 0으로 설정됨)|
| items | array of newsflash | 뉴스피드 기록 (지정된 조건으로 해당하는 뉴스피드 내용을 못 찾을 시, 비어있는 array를 반환하지만 오류 알람은 없음)|

각 newsflash가 포함하는 내용: 
| 명칭 | 유형 | 설명 | 
|----|----|----| 
| id | int | 뉴스피드 번호 | 
| title | string | 뉴스피드 제목 (미 삭제 뉴스피드 시, 내용/제목 중 하나가 올라감) |
| content | string | 뉴스피드 내용 (미 삭제 뉴스피드 시, 내용/제목 중 하나가 올라감) | 
| link | string | 뉴스피드 관련 내용 링크주소 | 
| link_title | string | 뉴스피드 관련 내용 링크주소의 링크제목 (link가 공백일 시, 해당 파라미터 생략）| 
| issue_time | int | 뉴스피드 업로드 시간 (이용자에게 보여질 시간) | 
| update_time | int | 뉴스피드 수정 시간 | 
| deleted | 0/1 | 삭제여부, 0: 미 삭제; 1: 삭제; 주의사항: 이미 삭제된 뉴스피드는 title과 content를 반환하지 않음 |

### getNewsflashUpdated
최근 8시간 내 수정 및 삭제된 뉴스피드 획득 (만약 업로드 후 미 수정 시 반환되지 않음)
#### 요청 마라미터
| 파라미터 | 유형 | 필수여부 | 디폴트값 | 설명 |
|----|----|----|----|----|
| language | string | 예 | 뉴스피드 언어 설정. COINNESS_LANGUAGE_* 를 사용하여 지정이 가능하다.
반환값 |

#### 명칭	유형	설명
| 명칭 | 유형 | 설명 |
|----|----|----|
| language | string | 요청한 뉴스피드의 언어 (요청 파라미터에서 받음) |
| count | int | 조건에 맞는 기록 수량 (start_time 미 제공 시，해당 파라미터는 0으로 설정됨)|
| items | array of newsflash | 뉴스피드 기록 (지정된 조건으로 해당하는 뉴스피드 내용을 못 찾을 시, 비어있는 array를 반환하지만 오류 알람은 없음)|

### 오류코드: 
| 오류 코드 | 오류 정보 | 설명 |
|----|----|----|
| 1001 | 서명인증실패 | sign 서명인증실패 |
| 1002 | 요청시간 초과 | 요청시간 timestamp와 현재 시스템시간 차이가 10초 이상일 때 (많거나 적게 10초를 초과할 때)）|
| 1003 | 지원하지 않는 언어 | 요청한 언어 language가 지원언어 범위 내에 없을 때 |