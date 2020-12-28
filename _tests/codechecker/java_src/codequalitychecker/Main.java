/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package codequalitychecker;

import javax.swing.*;

import java.util.*;
import java.io.*;

/**
 *
 * @author chris
 */
public class Main {

    // Settings
    public static boolean relay__api = true;
    public static boolean relay__mixed = false;
    public static boolean relay__checks = false;
    public static boolean relay__security = false;
    public static boolean relay__pedantic = false;
    public static boolean relay__spelling = false;
    public static String projectPath = ".." + File.separator + ".." + File.separator;
    public static String textEditorPath = "/usr/local/bin/geany";
    public static String phpPath = "php";

    // Memory
    static ArrayList<String> skipped_errors = new ArrayList<String>();

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // Load up UI
        try {
            String lf = UIManager.getCrossPlatformLookAndFeelClassName();
            UIManager.setLookAndFeel(lf);
        } catch (ClassNotFoundException exception) {
            exception.printStackTrace(System.out);
        } catch (IllegalAccessException exception) {
            exception.printStackTrace(System.out);
        } catch (InstantiationException exception) {
            exception.printStackTrace(System.out);
        } catch (UnsupportedLookAndFeelException exception) {
            exception.printStackTrace(System.out);
        }

        // Load settings
        Properties p = new Properties();
        try {
            p.load(new FileInputStream(System.getProperty("user.dir") + File.separator + "checker.ini"));
            relay__api = (p.getProperty("relay__api").equals("1"));
            relay__mixed = (p.getProperty("relay__mixed").equals("1"));
            relay__checks = (p.getProperty("relay__checks").equals("1"));
            relay__security = (p.getProperty("relay__security").equals("1"));
            relay__pedantic = (p.getProperty("relay__pedantic").equals("1"));
            relay__spelling = (p.getProperty("relay__spelling").equals("1"));
            projectPath = p.getProperty("projectPath");
            textEditorPath = p.getProperty("textEditorPath");
            phpPath = p.getProperty("phpPath");
            if (!new File(projectPath).exists()) {
                projectPath = ".." + File.separator + ".." + File.separator;
            }
            if (!new File(textEditorPath).exists()) {
                List<String> pathsToSearch = Arrays.asList(
                    "C:\\Program Files\\jedit\\jedit.exe",
                    "/Applications/jEdit.app/Contents/MacOS/jedit",
                    "/usr/local/bin/jedit",
                    "/usr/bin/jedit",
                    "C:\\Program Files\\Notepad++\\notepad++.exe",
                    "/usr/local/bin/geany",
                    "/usr/bin/geany",
                    "/usr/bin/kate",
                    "/usr/local/bin/bbedit",
                    "C:\\Program Files (x86)\\Codelobster Software\\CodelobsterPHPEdition\\ClPhpEd.exe",
                    "C:\\Program Files (x86)\\Codelobster Software\\CodeLobster IDE\\CodeLobsterIDE.exe",
                    "/Applications/CodeLobsterIDE.app/Contents/MacOS/CodeLobsterIDE",
                    "/usr/bin/codelobster", // TODO: Add line number support, once told about it
                    "C:\\Program Files\\NetBeans 8.2\\bin\\netbeans.exe",
                    "/Applications/NetBeans/NetBeans 8.2.app/Contents/Resources/NetBeans/bin/netbeans",
                    "/usr/local/bin/netbeans",
                    "C:\\Users\\IEUser\\AppData\\Local\\atom\\atom.exe",
                    "/Applications/Atom.app/Contents/Resources/app/atom.sh",
                    "/usr/bin/atom",
                    "/usr/local/bin/atom",
                    "C:\\Program Files\\Microsoft VS Code\\Code.exe",
                    "/usr/local/bin/code",
                    "C:\\Program Files (x86)\\PSPad editor\\PSPad.exe"
                );
                for (String pathToSearch : pathsToSearch) {
                    if (new File(pathToSearch).exists()) {
                        textEditorPath = pathToSearch;
                        break;
                    }
                }
            }
        } catch (IOException e) {
        } // No saving then

        // Load skipped
        try {
            BufferedReader skipFile = new BufferedReader(new FileReader(
                    System.getProperty("user.dir") + File.separator + "non_errors.txt"));
            String line = skipFile.readLine();
            while (line != null) {
                skipped_errors.add(line);
                line = skipFile.readLine();
            }
        } catch (IOException e) {
        } // No skip-saving then

        MainDialog d = new MainDialog();
    }

}
