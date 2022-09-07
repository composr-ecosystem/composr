package com.composrfoundation.codequalitychecker;

import javax.swing.*;

import java.util.*;
import java.io.*;

public class Main {

    // Settings
    public static boolean relay__api = true;
    public static boolean relay__todo = true;
    public static boolean relay__mixed = false;
    public static boolean relay__pedantic = false;
    public static boolean relay__somewhat_pedantic = true;
    public static boolean relay__security = false;
    public static boolean relay__manual_checks = false;
    public static boolean relay__spelling = false;
    public static boolean relay__codesniffer = false;
    public static boolean relay__eslint = false;
    public static String basePath = ".." + File.separator + ".." + File.separator;
    public static String phpPath = "php";
    public static String textEditorPath = null;

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
        } catch (ClassNotFoundException | IllegalAccessException | InstantiationException | UnsupportedLookAndFeelException exception) {
            exception.printStackTrace(System.out);
        }

        // Load settings
        Properties p = new Properties();
        try {
            p.load(new FileInputStream(System.getProperty("user.dir") + File.separator + "codechecker.ini"));
            try {
                relay__api = (p.getProperty("relay__api").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__todo = (p.getProperty("relay__todo").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__mixed = (p.getProperty("relay__mixed").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__pedantic = (p.getProperty("relay__pedantic").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__somewhat_pedantic = (p.getProperty("relay__somewhat_pedantic").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__security = (p.getProperty("relay__security").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__manual_checks = (p.getProperty("relay__manual_checks").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__spelling = (p.getProperty("relay__spelling").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__codesniffer = (p.getProperty("relay__codesniffer").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                relay__eslint = (p.getProperty("relay__eslint").equals("1"));
            }
            catch (NullPointerException e) {
            }
            try {
                basePath = p.getProperty("basePath");
                if (!new File(basePath).exists()) {
                    basePath = ".." + File.separator + ".." + File.separator;
                }
            }
            catch (NullPointerException e) {
            }
            try {
                phpPath = p.getProperty("phpPath");
            }
            catch (NullPointerException e) {
            }
            try {
                textEditorPath = p.getProperty("textEditorPath");
            }
            catch (NullPointerException e) {
                textEditorPath = null;
            }
        } catch (IOException e) {
        }

        if ((textEditorPath == null) || (!new File(textEditorPath).exists())) {
            List<String> pathsToSearch = Arrays.asList(
                "C:\\Program Files\\Notepad++\\notepad++.exe",

                "C:\\Program Files (x86)\\PSPad editor\\PSPad.exe",

                "/usr/local/bin/geany",
                "/usr/bin/geany",

                "/usr/local/bin/kate",
                "/usr/bin/kate",

                "/usr/local/bin/jedit",
                "/usr/bin/jedit",
                "/Applications/jEdit.app/Contents/MacOS/jedit",
                "C:\\Program Files\\jedit\\jedit.exe",

                "/usr/local/bin/bbedit",

                "/usr/bin/codelobster", // TODO: Add line number support, once told about it
                "/Applications/CodeLobsterIDE.app/Contents/MacOS/CodeLobsterIDE",
                "C:\\Program Files (x86)\\Codelobster Software\\CodelobsterPHPEdition\\ClPhpEd.exe",
                "C:\\Program Files (x86)\\Codelobster Software\\CodeLobster IDE\\CodeLobsterIDE.exe",

                "/usr/local/bin/netbeans",
                "/Applications/NetBeans/NetBeans 8.2.app/Contents/Resources/NetBeans/bin/netbeans",
                "C:\\Program Files\\NetBeans 8.2\\bin\\netbeans.exe",

                "/usr/local/bin/atom",
                "/usr/bin/atom",
                "/Applications/Atom.app/Contents/Resources/app/atom.sh",
                "C:\\Users\\IEUser\\AppData\\Local\\atom\\atom.exe",

                "/usr/local/bin/code",
                "C:\\Program Files\\Microsoft VS Code\\Code.exe"
            );
            for (String pathToSearch : pathsToSearch) {
                if (new File(pathToSearch).exists()) {
                    textEditorPath = pathToSearch;
                    break;
                }
            }
        }

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
        }

        MainDialog d = new MainDialog();
    }

}
