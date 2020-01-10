![logo](https://i.imgur.com/YAME6yT.png "#FrostyBot")

## Create an Ubuntu Lightsail Instance on Amazon Web Services

This document is a quick and dirty walkthrough to show you how to set up a Amazon Lightsail Ubuntu VPS for use with Frostybot. 

* You will need to signup at [Amazon Lightsail](https://lightsail.aws.amazon.com/) (it's only about $3.50 per month). 

* Once you have signed up and logged in, you will see your Lightsail dashboard.

  ![Lightsail1](https://i.imgur.com/JsTXxQ0.png)

* Click on the Create Instance button to create a new instance. Under the Pick Your Image Instance section, select Linux/Unix, then select OS Only, and then select Ubuntu 18.04, in the order as indicated on this screenshot:

  ![Lightsail2](https://i.imgur.com/e65cVF6.png)

* Under the Choose your Instance Plan section, select the cheapest option ($3.50 a month). This is more than sufficient to run Frostybot.

  ![Lightsail3](https://i.imgur.com/eIv78wn.png)

* Lastly, give your new instance a name under the "Identify your Instance" section

  ![Lightsail4](https://i.imgur.com/MuS401l.png)
  
* Then click the Create Instance button at the bottom to create the instance. It should take about 2 or 3 minutes to complete. While the instance is being created, its status will be "Pending" like in the screenshot below:

  ![Lightsail5](https://i.imgur.com/V5AFi1G.png)

* Once the instance has been created, its status will change to "Running". You can also see the IP address assigned to the instance. In this case its 52.28.96.113. This IP address is dynamic and will change every time your reboot the instance. For this reason we cannot use it for Frostybot, as Tradingview will not know which IP address to send the commands to.

  ![Lightsail6](https://i.imgur.com/5UT7vP2.png)

* What we need to do now is create a static IP address that does not change when your reboot the instance.
