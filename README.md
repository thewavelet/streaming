# streaming
ffmpeg,ffserver streaming commands


/etc/network/interfaces

auto eth0

iface eth0 inet static

  address 106.242.202.189
  
  netmask 255.255.255.0
  
  gateway 106.242.202.185
  

/etc/resolv.conf

nameserver xxx <- this would be automatically added.


/etc/init.d/networking restart


cd ~

apt-get install git

git clone https://github.com/candicom/streaming.git

sudo bash ./streamingserver.sh

ffserver -f ffserver.conf

