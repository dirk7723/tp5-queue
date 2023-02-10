<?php


namespace app\queue\redis;


use think\Controller;
use think\queue\Job;
class Luka extends Controller
{

    /**
     * think-queue固定执行的方法
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job,$data){
        // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }
        //处理业务
        $isJobDone = self::handleJob($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            echo("<info>Luka Job 已经结束"."</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                echo("<warn>Luka Job 已经重试三次了!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean 任务执行的结果
     */
    public static function handleJob($data) {
        // 根据消息中的数据进行实际的业务处理...
        $uid = !empty($data['uid']) ? $data['uid'] : 0;
        $note_url = !empty($data['note_url']) ? $data['note_url'] : 0;
        echo "{$uid}博主的笔记{$note_url}正在处理，请稍等\n";
        return true;
    }
}