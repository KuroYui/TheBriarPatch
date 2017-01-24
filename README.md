# TheBriarPatch
<center>Beta version has been uploaded!</center> 
<img src="https://raw.githubusercontent.com/musicmancorley/TheBriarPatch/master/thebriarpatch.png"><br>
<b>Demo video:</b> https://youtu.be/vzZACYVJA3Y
<br>
I am creating and adding to the WIKI even as I type this.  Stay tuned in for many updates to the WIKI and this github repo!
There is much work to be done, but here is the first fruits of TheBriarPatch for your exploration.  I'll try and answer what I am guessing will be common questions about TheBriarPatch below:<br>
1. What all devices can it log?<br>
As of right now, I have Windows, Apple(iphone only), Linux [Pi (armv7l), ubuntu/debian, smarttv(TIZEN), android(chromebook and mobile).  More to come as I continue developing this solution
2. What is that malicious scanner option that I noticed in the install script?<br>
Good question.  This will take your suricata http traffic and compare with Bro's intel logs to determine if any of the sites visited were marked as malicious in the Bro Intel feed.  Keep in mind you will need to get some malware feeds pulled into your Intel feed from critical-stack.  I have that info on the BriarIDS wiki.
Once the malicious scanning option is enabled, you will notice a new column added into the displayed results.  Also keep in mind that if this option is enabled, it can take some time to compare all traffic to bro's intel log but it is a nice reliable way of discovering malicious traffic in a LIVE setting.
3. If I enabled auto-refresh and malicious scanning in the install script, how can I disable them?<br>
easy.  do this: sudo nano maliciousscanning and change the '1' to a '0'.  Do the same for the 'refreshornot' file.
4. How about archiving logs?<br>
This is something I am also currently working on.  Right now TheBriarPatch is more a LIVE logging solution.  It will pull in logs that are currently in the suricata logs directory,http.log, even if suricata isn't running.  That is really the only "archiving" feature enabled right now.
<br>

