#!/bin/bash
> hostapd.conf

echo "interface=wlan0
ctrl_interface=/var/run/hostapd
ssid=$1
channel=9

# WPA and WPA2 configuration
# macaddr_acl=0
# auth_algs=1
# wpa=2
# wpa_passphrase=cowbox
# wpa_key_mgmt=WPA-PSK
# wpa_pairwise=TKIP
# rsn_pairwise=CCMP

# Hardware configuration
driver=rtl871xdrv
beacon_int=100

hw_mode=g
#ieee80211n=1

#wme_enabled=1
#LFE

#ht_capab=[SHORT-GI-20][SHORT-GI-40][HT40+]

#LFE
max_num_sta=30" >> hostapd.conf
