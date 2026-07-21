$WshShell = New-Object -ComObject WScript.Shell
$ShortcutPath = "C:\Users\shubh\OneDrive\Desktop\DineFlow AI POS.lnk"
$Shortcut = $WshShell.CreateShortcut($ShortcutPath)
$Shortcut.TargetPath = "d:\DineFlow AI\START_DINEFLOW_POS.bat"
$Shortcut.WorkingDirectory = "d:\DineFlow AI"
$Shortcut.IconLocation = "d:\DineFlow AI\dineflow_logo.ico"
$Shortcut.Description = "Start DineFlow AI POS Application"
$Shortcut.Save()
Write-Host "Desktop Shortcut created with DineFlow Logo Icon!"
