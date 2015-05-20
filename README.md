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




sudo apt-get update -y
sudo apt-get remove libav-tools
sudo add-apt-repository ppa:mc3man/trusty-media
sudo apt-get update
sudo apt-get install ffmpeg gstreamer0.10-ffmpeg gstreamer0.10-fluendo-mp3 gstreamer0.10-gnonlin gstreamer0.10-plugins-bad-multiverse gstreamer0.10-plugins-bad gstreamer0.10-plugins-ugly totem-plugins-extra gstreamer-tools ubuntu-restricted-extras libxine1-ffmpeg gxine mencoder mpeg2dec vorbis-tools id3v2 mpg321 mpg123 libflac++6 totem-mozilla icedax tagtool easytag id3tool lame nautilus-script-audio-convert libmad0 libjpeg-progs flac faac faad sox ffmpeg2theora libmpeg2-4 uudeview flac libmpeg3-1 mpeg3-utils mpegdemux liba52-0.7.4-dev libquicktime2





ffserver.conf

HTTPPort 8090
HTTPBindAddress 0.0.0.0
MaxHTTPConnections 2000
MaxClients 1000
MaxBandwidth 100000
CustomLog -

<Feed streamwebm.ffm>
  File ./streamwebm.ffm
  FileMaxSize 50M
</Feed>

<Stream streamwebm>  
http://localhost:8090/streamwebm.ffm
Feed streamwebm.ffm
Format webm

VideoFrameRate 25
VideoSize 640x480

AudioSampleRate 48000
AVOptionAudio flags +global_header

MaxTime 0
AVOptionVideo me_range 16
AVOptionVideo qdiff 4
AVOptionVideo qmin 4
AVOptionVideo qmax 40
AVOptionVideo flags +global_header

PreRoll 10
StartSendOnKey

</Stream>


<Stream stat.html>
  Format status
</Stream>


<Redirect index.html>
  URL http://yoururl.com
</Redirect>
