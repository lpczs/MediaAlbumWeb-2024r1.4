<?php
class weChatSessionUpdates extends ExternalScript
{
    public function run()
    {
        if($this->mode == 'upgrade')
        {
            $this->addSessionChange(array('path' => array('order'),
											'changes' => array(
												array('key' => 'ccicachefileneeded', 'insert' => "0")
											)));
        }
    }
}
?>