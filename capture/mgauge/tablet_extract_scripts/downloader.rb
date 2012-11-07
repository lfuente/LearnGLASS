#!/bin/env ruby
# encoding: utf-8
require 'open-uri'
require 'fileutils'
require 'json'

puts "  ~ mGauge Log Downloader ~"
puts "Conecta el tablet y pulsa Enter."
gets
puts "Reiniciando servidor ADB.."
`./adb kill-server`
`./adb start-server`
puts "Iniciando conexión..."
s = `./adb forward tcp:8888 tcp:8888`
if $?.exitstatus == 1
	puts "Error conectando con el tablet."
	exit
end

puts "Obteniendo número de serie del tablet.."
dev = `./adb devices`
serial = dev.split("\n")[1].split("\t")[0]
puts "Número de serie: #{serial}. Creando carpeta..."
FileUtils.mkdir_p "output/#{serial}"
loop do
	puts "Comprobando conexión a servidor CouchDB.."
	begin
		s = open("http://localhost:8888/mgauge-logs").read
		puts "OK!"
		break
	rescue
		puts "Error conectando a CouchDB. Reiniciando aplicación..."
		`./adb shell am force-stop es.uc3m.gast.gradient.mgauge.cosmo/.CosmoEEEActivity`
		`./adb shell am start -n es.uc3m.gast.gradient.mgauge.cosmo/.CosmoEEEActivity`
		puts "Esperando 5 segundos..."
		sleep 5
	end
end

print "Obteniendo logs..."
logs = open("http://localhost:8888/mgauge-logs/_all_docs?include_docs=true").read
File.open("output/#{serial}/logs.json","w") { |log|
	log.write logs
}
puts "[OK]"
puts "Obteniendo imágenes..."
`./adb pull /sdcard/DCIM/Camera output/#{serial}/`


