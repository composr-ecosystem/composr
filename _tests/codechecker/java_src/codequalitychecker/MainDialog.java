package codequalitychecker;

import java.awt.*;
import javax.swing.*;
import java.awt.datatransfer.*;
import java.awt.event.ActionEvent;
import java.awt.event.KeyEvent;
import java.awt.event.MouseEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyListener;
import java.awt.event.MouseListener;

import java.util.*;
import java.io.*;

/**
 * <p>
 * Title: Java Front-end to Web Code Quality Checker</p>
 */
public class MainDialog extends JFrame {

    // UI

    JPanel panel1 = new JPanel();
    JButton templatesBtn = new JButton();
    JButton scanFilesBtn = new JButton();
    JButton examineFilesBtn = new JButton();
    JButton forgetErrorBtn = new JButton();
    JButton viewCodeBtn = new JButton();
    JButton clearErrorsBtn = new JButton();
    JButton aboutBtn = new JButton();
    JButton optionsBtn = new JButton();
    JButton scanSignaturesBtn = new JButton();
    JButton countBtn = new JButton();
    JList files = null;
    JList errors = null;
    JLabel jLabel1 = new JLabel();
    JLabel jLabel2 = new JLabel();

    public MainDialog(String title) {
        this.setTitle(title);
        try {
            setDefaultCloseOperation(DISPOSE_ON_CLOSE);
            jbInit();
            pack();
        } catch (Exception exception) {
            exception.printStackTrace(System.out);
        }
    }

    public MainDialog() {
        this("Web Code Quality Checker");
    }

    private void jbInit() throws Exception {
        this.setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);
        this.setResizable(false);

        DefaultListModel model1 = new DefaultListModel();
        files = new JList(model1);
        DefaultListModel model2 = new DefaultListModel();
        errors = new JList(model2);
        JScrollPane scrollPaneFiles = new JScrollPane(files);
        JScrollPane scrollPaneErrors = new JScrollPane(errors);

        errors.setTransferHandler(new FSTransfer(this));
        errors.setDragEnabled(false);

        panel1.setLayout(null);
        templatesBtn.setBounds(new Rectangle(8, 557, 107, 19));
        templatesBtn.setMargin(new Insets(0, 0, 0, 0));
        templatesBtn.setActionCommand("templatesBtn");
        templatesBtn.setText("<html>Scan for HTML</html>");
        templatesBtn.addActionListener(new Dialog1_templatesBtn_actionAdapter(this));
        templatesBtn.setBackground(new Color(215, 245, 229));
        scanFilesBtn.setBounds(new Rectangle(8, 538, 107, 19));
        scanFilesBtn.setMargin(new Insets(0, 0, 0, 0));
        scanFilesBtn.setActionCommand("scanFilesBtn");
        scanFilesBtn.setText("<html>Scan for code</html>");
        scanFilesBtn.addActionListener(new Dialog1_scanFilesBtn_actionAdapter(this));
        scanFilesBtn.setBackground(new Color(215, 245, 229));
        examineFilesBtn.setBounds(new Rectangle(194, 538, 78, 37));
        examineFilesBtn.setMargin(new Insets(0, 0, 0, 0));
        examineFilesBtn.setToolTipText("");
        examineFilesBtn.setActionCommand("examineFilesBtn");
        examineFilesBtn.setMnemonic('0');
        examineFilesBtn.setSelectedIcon(null);
        examineFilesBtn.setText("<html>Examine Selection</html>");
        examineFilesBtn.setBackground(new Color(215, 245, 229));
        examineFilesBtn.addActionListener(new Dialog1_examineFilesBtn_actionAdapter(this));
        forgetErrorBtn.setBounds(new Rectangle(504, 538, 58, 37));
        forgetErrorBtn.setMargin(new Insets(0, 0, 0, 0));
        forgetErrorBtn.setActionCommand("ForgetErrorBtn");
        forgetErrorBtn.setText("<html>Forget error</html>");
        forgetErrorBtn.setBackground(new Color(248, 247, 198));
        forgetErrorBtn.addActionListener(new Dialog1_ForgetErrorBtn_actionAdapter(this));
        viewCodeBtn.setBounds(new Rectangle(410, 538, 89, 37));
        viewCodeBtn.setMargin(new Insets(0, 0, 0, 0));
        viewCodeBtn.setActionCommand("ViewCodeBtn");
        viewCodeBtn.setText("<html>View code in editor</html>");
        viewCodeBtn.addActionListener(new Dialog1_ViewCodeBtn_actionAdapter(this));
        viewCodeBtn.setBackground(new Color(248, 247, 198));
        clearErrorsBtn.setBounds(new Rectangle(560, 538, 75, 37));
        clearErrorsBtn.setMargin(new Insets(0, 0, 0, 0));
        clearErrorsBtn.setActionCommand("ClearErrorsBtn");
        clearErrorsBtn.addActionListener(new Dialog1_ClearErrorsBtn_actionAdapter(this));
        clearErrorsBtn.setText("<html>Clear error list</html>");
        aboutBtn.setBounds(new Rectangle(644, 538, 65, 37));
        aboutBtn.setMargin(new Insets(0, 0, 0, 0));
        aboutBtn.setActionCommand("aboutBtn");
        aboutBtn.setText("<html>About</html>");
        aboutBtn.addActionListener(new Dialog1_aboutBtn_actionAdapter(this));
        optionsBtn.setBounds(new Rectangle(568, 538, 72, 37));
        optionsBtn.setMargin(new Insets(0, 0, 0, 0));
        optionsBtn.setActionCommand("optionsBtn");
        optionsBtn.setText("<html>Options</html>");
        optionsBtn.addActionListener(new Dialog1_optionsBtn_actionAdapter(this));
        scanSignaturesBtn.setBounds(new Rectangle(277, 538, 128, 37));
        scanSignaturesBtn.setMargin(new Insets(0, 0, 0, 0));
        scanSignaturesBtn.setActionCommand("scanSignaturesBtn");
        scanSignaturesBtn.setText("<html>Compile function signatures (PHP)</html>");
        scanSignaturesBtn.setBackground(new Color(215, 245, 229));
        scanSignaturesBtn.addActionListener(new Dialog1_scanSignaturesBtn_actionAdapter(this));
        countBtn.setBounds(new Rectangle(122, 538, 67, 37));
        countBtn.setMargin(new Insets(0, 0, 0, 0));
        countBtn.setActionCommand("countBtn");
        countBtn.setText("<html>Line Count</html>");
        countBtn.addActionListener(new Dialog1_countBtn_actionAdapter(this));
        countBtn.setBackground(new Color(215, 245, 229));
        panel1.setMinimumSize(new Dimension(790, 590));
        files.setBackground(new Color(215, 245, 229));
        files.setSelectionMode(ListSelectionModel.MULTIPLE_INTERVAL_SELECTION);
        files.addKeyListener(new Dialog1_files_actionAdapterKey(this));
        files.addMouseListener(new Dialog1_files_actionAdapterClick(this));
        scrollPaneFiles.setBounds(new Rectangle(10, 14, 389, 517));
        errors.setBackground(new Color(248, 247, 198));
        errors.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        errors.addKeyListener(new Dialog1_errors_actionAdapterKey(this));
        errors.addMouseListener(new Dialog1_errors_actionAdapterClick(this));
        scrollPaneErrors.setBounds(new Rectangle(406, 14, 885, 517));
        jLabel1.setToolTipText("");
        jLabel1.setText("<html>This is your project workspace. Set it by setting your code directory in the options.</html>");
        jLabel1.setBounds(new Rectangle(26, 490, 339, 37));
        jLabel2.setText(
            "<html>Either double click a file from the left, click examine selected "
            + "files, or just drag and drop files here from your OS.<br>"
            + "<br>Any errors will show up in this pane, and you\'ll be able "
            + "to view the involved code in the chosen editor via a double-click.</html>");
        jLabel2.setBounds(new Rectangle(415, 380, 350, 130));
        panel1.add(aboutBtn);
        panel1.add(countBtn);
        panel1.add(examineFilesBtn);
        panel1.add(scanSignaturesBtn);
        panel1.add(viewCodeBtn);
        panel1.add(forgetErrorBtn);
        //panel1.add(ClearErrorsBtn);
        panel1.add(optionsBtn);
        panel1.add(scanFilesBtn);
        panel1.add(templatesBtn);
        panel1.add(jLabel1);
        panel1.add(scrollPaneFiles);
        panel1.add(jLabel2);
        panel1.add(scrollPaneErrors);
        this.getContentPane().add(panel1, java.awt.BorderLayout.WEST);
        panel1.setPreferredSize(new Dimension(1300, 590));

        this.setVisible(true);

        // Loading of file list
        EventQueue.invokeLater(new Runnable() {
            @Override
            public void run() {
                scanFilesBtn.doClick();
            }
        });
    }

    public void initiateFileSearch(String type) {
        boolean sort_new = false, skip_custom = false;

        String path = Main.projectPath.replace("\"", "");
        if ((path.equals(".")) || (path.equals("./")) || (path.equals(".\\"))) {
            path = "";
        }

        int n;
        n = JOptionPane.showOptionDialog(
            this,
            "Would you like to skip files underneath any directory with '*_custom' in the name?",
            "Question", JOptionPane.YES_NO_OPTION,
            JOptionPane.QUESTION_MESSAGE,
            null,
            null,
            null
        );
        if (n == JOptionPane.YES_OPTION) {
            skip_custom = true;
        }
        n = JOptionPane.showOptionDialog(
            this,
            "Would you like to sort by date?",
            "Question", JOptionPane.YES_NO_OPTION,
            JOptionPane.QUESTION_MESSAGE,
            null,
            null,
            null
        );
        if (n == JOptionPane.YES_OPTION) {
            sort_new = true;
        }

        ((DefaultListModel) this.files.getModel()).removeAllElements();

        ArrayList<SearchFile> finalFiles = initiateFileSearch(type, path, "", skip_custom);
        if (sort_new) {
            Collections.sort(finalFiles);
        }

        for (SearchFile next : finalFiles) {
            ((DefaultListModel) this.files.getModel()).addElement(next.path);
        }
    }

    private ArrayList<SearchFile> initiateFileSearch(String type, String path, String rec_subpath, boolean skip_custom) {
        Date d = new Date();
        long currentTime = d.getTime() / 1000;
        ArrayList<SearchFile> finalFiles = new ArrayList();

        File myFile = new File(path);
        String[] theFiles = myFile.list();
        if (theFiles == null) {
            JOptionPane.showMessageDialog(this, "Could not search the directory " + path + ".");
            return finalFiles;
        }
        Arrays.sort(theFiles);
        int i;
        long last_m;
        File tmpFile;
        for (i = 0; i < theFiles.length; i++) {
            if (theFiles[i].equals(".") || theFiles[i].equals("..")) {
                continue;
            }

            tmpFile = new File(path + File.separator + theFiles[i]);

            last_m = tmpFile.lastModified() / 1000 + 60 * 60 * 24;

            if (tmpFile.isDirectory()) {
                // Similar to IGNORE_FLOATING
                if ((theFiles[i].equals("_meta_tree"))
                        || (theFiles[i].equals("tracker"))
                        || (theFiles[i].equals("exports"))
                        || (theFiles[i].equals("ckeditor"))
                        || (theFiles[i].equals("simpletest"))) {
                    continue;
                }

                // Similar to IGNORE_NONBUNDLED
                if ((skip_custom) && (
                        (theFiles[i].equals("uploads"))
                        || (theFiles[i].equals("_tests"))
                        || (theFiles[i].equals("mobiquo"))
                        || (theFiles[i].equals("ocproducts"))
                        || (theFiles[i].equals("buildr"))
                        || (theFiles[i].contains("_custom")))) {
                    continue;
                }

                // Recurse
                ArrayList<SearchFile> next = initiateFileSearch(type, tmpFile.getAbsolutePath(), rec_subpath + ((rec_subpath.equals("")) ? "" : File.separator) + tmpFile.getName(), skip_custom);
                finalFiles.addAll(next);
            } else if (tmpFile.isFile()) {
                // Similar to IGNORE_SHIPPED_VOLATILE
                if (theFiles[i].equals("_config.php") || theFiles[i].equals("errorlog.php")) {
                    continue;
                }

                // Similar to IGNORE_ACCESS_CONTROLLERS
                if (tmpFile.length() == 0) {
                    continue;
                }

                 // Filter by file type
                if ((type.equals("PHP")) && (!theFiles[i].toLowerCase().endsWith(".php"))) {
                    continue;
                }
                if ((type.equals("HTML"))
                        && (!theFiles[i].toLowerCase().endsWith(".css"))
                        && (!theFiles[i].toLowerCase().endsWith(".js"))
                        && (!theFiles[i].toLowerCase().endsWith(".html"))
                        && (!theFiles[i].toLowerCase().endsWith(".htm"))
                        && (!theFiles[i].endsWith(".tpl"))
                        && (!theFiles[i].toLowerCase().endsWith(".ini"))) {
                    continue;
                }

                // Add to file list
                SearchFile mySearchFile = new SearchFile(rec_subpath + ((rec_subpath.equals("")) ? "" : File.separator) + tmpFile.getName(), tmpFile.lastModified());
                finalFiles.add(mySearchFile);
            }
        }

        return finalFiles;
    }

    public void aboutBtn_actionPerformed(ActionEvent e) {
        new AboutDialog().setVisible(true);
    }

    public void optionsBtn_actionPerformed(ActionEvent e) {
        new OptionsDialog().setVisible(true);
    }

    public void scanFilesBtn_actionPerformed(ActionEvent e) {
        initiateFileSearch("PHP");
    }

    public void scanSignaturesBtn_actionPerformed(ActionEvent e) {
        ((DefaultListModel) errors.getModel()).removeAllElements();
        executePHPfile("phpdoc_parser.php path=\"\"" + Main.projectPath + "\"");
    }

    public void templatesBtn_actionPerformed(ActionEvent e) {
        initiateFileSearch("HTML");
    }

    public void errors_actionPerformedKey(KeyEvent e) {
        if (e.getKeyChar() == '\n') {
            viewCodeLine();
        }
    }

    public void errors_actionPerformedMouse(MouseEvent e) {
        if ((e.getButton() == MouseEvent.BUTTON1) && (e.getClickCount() == 2)) {
            viewCodeLine();
        }
    }

    public void countBtn_actionPerformed(ActionEvent e) {
        int count = 0;
        int i;
        DefaultListModel listModel = (DefaultListModel) this.files.getModel();
        BufferedReader myReader;
        String line;
        for (i = 0; i < listModel.getSize(); i++) {
            if (!this.files.isSelectedIndex(i)) {
                continue;
            }

            try {
                myReader = new BufferedReader(new FileReader(Main.projectPath + File.separator + (String) listModel.getElementAt(i)));
                line = myReader.readLine();
                while (line != null) {
                    if (!line.equals("")) {
                        count++;
                    }
                    line = myReader.readLine();
                }
            } catch (IOException e2) {
            } // No contribution to count
        }

        JOptionPane.showMessageDialog(this, "There are " + count + " lines of code in these files (excluding blank lines).");
    }

    private ArrayList<String> decompose_line(String line) {
        ArrayList<String> decomposed = new ArrayList<String>();

        int i, num = 0;
        String current = "";
        boolean inquotes = false;
        String cchar;

        for (i = 0; i < line.length(); i++) {
            cchar = line.substring(i, i + 1);

            if (num == 4) {
                inquotes = true;
            }
            if ((cchar.equals("\"")) && (num < 4)) {
                inquotes = !inquotes;
            } else {
                if ((cchar.equals(" ")) && (!inquotes)) {
                    decomposed.add(current);
                    num++;
                    current = "";
                } else {
                    current = current + cchar;
                }
            }
        }
        decomposed.add(current);

        return decomposed;
    }

    private boolean line_skippable(String line) {
        ArrayList<String> decomposed = decompose_line(line);
        ArrayList<String> skip_decomposition;

        boolean same_0, same_1, same_3, same_4;
        int val_2_a, val_2_b;

        int i;
        for (i = 0; i < Main.skipped_errors.size(); i++) {
            skip_decomposition = decompose_line((String) Main.skipped_errors.get(i));

            if (decomposed.size() < 5) {
                continue;
            }

            same_0 = ((String) decomposed.get(0)).equals((String) skip_decomposition.get(0));
            same_1 = ((String) decomposed.get(1)).equals((String) skip_decomposition.get(1));
            same_3 = ((String) decomposed.get(3)).equals((String) skip_decomposition.get(3));
            same_4 = ((String) decomposed.get(4)).equals((String) skip_decomposition.get(4));
            try {
                val_2_a = Integer.parseInt((String) decomposed.get(2));
                val_2_b = Integer.parseInt((String) skip_decomposition.get(2));
                if ((same_0) && (same_1) && (val_2_a > val_2_b - 10) && (val_2_a < val_2_b + 10) && (same_3) && (same_4)) {
                    return true;
                }
            } catch (NumberFormatException e) {
            }
        }

        return false;
    }

    public void files_actionPerformedKey(KeyEvent e) {
        if (e.getKeyChar() == '\n') {
            ((DefaultListModel) errors.getModel()).removeAllElements();
            Object sv[] = new Object[1];
            sv[0] = files.getSelectedValue();
            do_execution(sv);
        }
    }

    public void files_actionPerformedMouse(MouseEvent e) {
        if ((e.getButton() == MouseEvent.BUTTON1) && (e.getClickCount() == 2)) {
            ((DefaultListModel) errors.getModel()).removeAllElements();
            Object sv[] = new Object[1];
            sv[0] = files.getSelectedValue();
            do_execution(sv);
        }
    }

    public void files_actionPerformed(ActionEvent e) {
        ((DefaultListModel) errors.getModel()).removeAllElements();
        Object[] sv = (Object[]) this.files.getSelectedValues();
        do_execution(sv);
    }

    public void do_execution(Object[] sv) {
        do_execution(sv, false);
    }

    public void executePHPfile(String line) {
        Dialog tempProgress = new ProcessingDialog();
        tempProgress.setVisible(true);

        line = Main.phpPath + " " + line;
        System.out.println(line);
        try {
            Process execution = Runtime.getRuntime().exec(line);
            InputStream stream = execution.getInputStream();
            byte[] bytes = new byte[1024];
            String result = "";

            try {
                Thread.sleep(300);
            } catch (InterruptedException ex) {
            }

            int test = 0;
            while (test != -1) {
                test = stream.read(bytes);
                if (test != -1) {
                    result = result + new String(bytes, 0, test);
                }
            }
            String[] results = result.split("\n");
            int j;
            for (j = 0; j < results.length; j++) {
                if (!line_skippable(results[j])) {
                    ((DefaultListModel) errors.getModel()).addElement(results[j]);
                }
            }
            this.jLabel1.setVisible(false);
            this.jLabel2.setVisible(false);
        } catch (java.io.IOException e2) {
            JOptionPane.showMessageDialog(
                this,
                "Failure executing PHP backend. (" + e2.toString() + ")",
                "Error",
                JOptionPane.ERROR_MESSAGE
            );
            tempProgress.setVisible(false);
            return;
        }

        tempProgress.setVisible(false);
    }

    public void do_execution(Object[] sv, boolean no_path) {
        int i, j;

        if (sv.length == 0) {
            JOptionPane.showMessageDialog(this, "No files were selected.", "Error", JOptionPane.ERROR_MESSAGE);
        }
        String line = "code_quality.php spacedpaths=1";
        if (!no_path) {
            line = line + " path=" + Main.projectPath;
        } else {
            line = line + " path=\"\"";
        }
        for (i = 0; i < sv.length; i++) {
            line = line + " " + ((String) sv[i]);
        }
        if (Main.relay__api) {
            line = line + " api=1";
        }
        if (Main.relay__checks) {
            line = line + " checks=1";
        }
        if (Main.relay__mixed) {
            line = line + " mixed=1";
        }
        if (Main.relay__pedantic) {
            line = line + " pedantic=1";
        }
        if (Main.relay__security) {
            line = line + " security=1";
        }
        if (Main.relay__spelling) {
            line = line + " spelling=1";
        }

        executePHPfile(line);
    }

    private void viewCodeLine() {
        if (this.errors.getSelectedIndex() == -1) {
            JOptionPane.showMessageDialog(this, "No line was selected.");
            return;
        }

        String selected = (String) this.errors.getSelectedValue();

        ArrayList decomposed = decompose_line(selected);

        if ((decomposed.size() < 4)
                || ((!((String) decomposed.get(1)).endsWith(".php"))
                && (!((String) decomposed.get(1)).endsWith(".css"))
                && (!((String) decomposed.get(1)).endsWith(".js"))
                && (!((String) decomposed.get(1)).endsWith(".htm"))
                && (!((String) decomposed.get(1)).endsWith(".html"))
                && (!((String) decomposed.get(1)).endsWith(".tpl"))
                && (!((String) decomposed.get(1)).endsWith(".ini")))) {
            JOptionPane.showMessageDialog(this, "This line was not a code referencing line, so I cannot open up an editor there.");
            return;
        }

        String params;
        String line;
        String filePath = (((((String) decomposed.get(1)).charAt(1) == ':') || (((String) decomposed.get(1)).charAt(0) == '/')) ? "" : Main.projectPath) + File.separator + decomposed.get(1);
        if (filePath.contains(" ")) {
            filePath = "\"" + filePath + "\"";
        }
        if (Main.textEditorPath.toLowerCase().endsWith("jedit.exe") || Main.textEditorPath.toLowerCase().endsWith("jedit")) {
            params = " +line:" + decomposed.get(2);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("notepad++.exe")) {
            params = " -multiInst " + filePath + " -n" + decomposed.get(2);
            line = Main.textEditorPath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("kate")) {
            params = " --line " + decomposed.get(2) + " --column " + decomposed.get(3);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("geany")) {
            params = " +" + decomposed.get(2);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("bbedit")) {
            params = " +" + decomposed.get(2);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("clphped.exe")) {
            params = " /g" + filePath + ":" + decomposed.get(2);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("codelobsteride.exe") || Main.textEditorPath.toLowerCase().endsWith("codelobsteride") || Main.textEditorPath.toLowerCase().endsWith("codelobster")) {
            params = ""; // TODO: Add line number support, once told about it
            line = Main.textEditorPath + " " + filePath + params;
        } else if ((Main.textEditorPath.toLowerCase().endsWith("netbeans.exe")) || (Main.textEditorPath.toLowerCase().endsWith("netbeans"))) {
            params = " --open " + filePath + ":" + decomposed.get(2);
            line = Main.textEditorPath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("mate")) {
            params = " -wl" + decomposed.get(2) + " " + filePath;
            line = Main.textEditorPath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("atom.exe") || Main.textEditorPath.toLowerCase().endsWith("atom.sh") || Main.textEditorPath.toLowerCase().endsWith("atom")) {
            params = ":" + decomposed.get(2);
            line = Main.textEditorPath + " " + filePath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("code.exe") || Main.textEditorPath.toLowerCase().endsWith("code")) {
            params = " -g " + filePath + ":" + decomposed.get(2);
            line = Main.textEditorPath + params;
        } else if (Main.textEditorPath.toLowerCase().endsWith("pspad.exe")) {
            params = " \\" + decomposed.get(2);
            line = Main.textEditorPath + params + " " + filePath;
        } else {
            params = "";
            line = Main.textEditorPath + " " + filePath + params;
        }

        if (this.errors.getSelectedIndex() == -1) {
            JOptionPane.showMessageDialog(this, "No file was selected.", "Error", JOptionPane.ERROR_MESSAGE);
        }
        try {
            Runtime.getRuntime().exec(line);
        } catch (java.io.IOException e) {
            JOptionPane.showMessageDialog(this, "Failure executing text editor.", "Error", JOptionPane.ERROR_MESSAGE);
        }
    }

    public void ViewCodeBtn_actionPerformed(ActionEvent e) {
        viewCodeLine();
    }

    public void ForgetErrorBtn_actionPerformed(ActionEvent e) {
        Main.skipped_errors.add((String) errors.getSelectedValue());
        DefaultListModel model = (DefaultListModel) errors.getModel();
        model.remove(errors.getSelectedIndex());
        String writePath = "non_errors.txt";

        // Save skipped
        try {
            FileWriter writer = new FileWriter(writePath);
            PrintWriter out = new PrintWriter(writer);
            int i;
            for (i = 0; i < Main.skipped_errors.size(); i++) {
                out.println((String) Main.skipped_errors.get(i));
            }
            out.close();
        } catch (IOException e2) {
        } // No skip-saving then
    }
}

class Dialog1_errors_actionAdapterKey implements KeyListener {

    private final MainDialog adaptee;

    Dialog1_errors_actionAdapterKey(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void keyTyped(KeyEvent e) {
        adaptee.errors_actionPerformedKey(e);
    }

    @Override
    public void keyPressed(KeyEvent e) {
    }

    @Override
    public void keyReleased(KeyEvent e) {
    }
}

class Dialog1_errors_actionAdapterClick implements MouseListener {

    private final MainDialog adaptee;

    Dialog1_errors_actionAdapterClick(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void mouseClicked(MouseEvent e) {

        adaptee.errors_actionPerformedMouse(e);
    }

    @Override
    public void mouseEntered(MouseEvent e) {
    }

    @Override
    public void mouseExited(MouseEvent e) {
    }

    @Override
    public void mousePressed(MouseEvent e) {
    }

    @Override
    public void mouseReleased(MouseEvent e) {
    }
}

class Dialog1_files_actionAdapterKey implements KeyListener {

    private final MainDialog adaptee;

    Dialog1_files_actionAdapterKey(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void keyTyped(KeyEvent e) {
        adaptee.files_actionPerformedKey(e);
    }

    @Override
    public void keyPressed(KeyEvent e) {
    }

    @Override
    public void keyReleased(KeyEvent e) {
    }
}

class Dialog1_files_actionAdapterClick implements MouseListener {

    private final MainDialog adaptee;

    Dialog1_files_actionAdapterClick(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void mouseClicked(MouseEvent e) {

        adaptee.files_actionPerformedMouse(e);
    }

    @Override
    public void mouseEntered(MouseEvent e) {
    }

    @Override
    public void mouseExited(MouseEvent e) {
    }

    @Override
    public void mousePressed(MouseEvent e) {
    }

    @Override
    public void mouseReleased(MouseEvent e) {
    }
}

class Dialog1_ClearErrorsBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_ClearErrorsBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        ((DefaultListModel) adaptee.errors.getModel()).clear();
    }
}

class Dialog1_ForgetErrorBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_ForgetErrorBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.ForgetErrorBtn_actionPerformed(e);
    }
}

class Dialog1_files_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_files_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.files_actionPerformed(e);
    }
}

class Dialog1_ViewCodeBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_ViewCodeBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {

        adaptee.ViewCodeBtn_actionPerformed(e);
    }
}

class Dialog1_examineFilesBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_examineFilesBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {

        adaptee.files_actionPerformed(e);
    }
}

class Dialog1_countBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_countBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.countBtn_actionPerformed(e);
    }
}

class Dialog1_templatesBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_templatesBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.templatesBtn_actionPerformed(e);
    }
}

class Dialog1_scanSignaturesBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_scanSignaturesBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.scanSignaturesBtn_actionPerformed(e);
    }
}

class Dialog1_scanFilesBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_scanFilesBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.scanFilesBtn_actionPerformed(e);
    }
}

class Dialog1_optionsBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_optionsBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.optionsBtn_actionPerformed(e);
    }
}

class Dialog1_aboutBtn_actionAdapter implements ActionListener {

    private final MainDialog adaptee;

    Dialog1_aboutBtn_actionAdapter(MainDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.aboutBtn_actionPerformed(e);
    }
}

class FSTransfer extends TransferHandler {

    private final MainDialog adaptee;

    public FSTransfer(MainDialog d) {
        this.adaptee = d;
    }

    @Override
    public boolean importData(JComponent comp, Transferable t) {
        // Make sure we have the right starting points
        if (!t.isDataFlavorSupported(DataFlavor.javaFileListFlavor)) {
            return false;
        }

        // Grab the tree, its model and the root node
        try {
            java.util.List data = (java.util.List) t.getTransferData(DataFlavor.javaFileListFlavor);
            Object[] sl = data.toArray();
            if (adaptee != null) {
                String[] sl2 = new String[sl.length];
                int i;
                for (i = 0; i < sl.length; i++) {
                    sl2[i] = ((File) sl[i]).getAbsolutePath();
                }
                ((DefaultListModel) adaptee.errors.getModel()).
                        removeAllElements();
                adaptee.do_execution(sl2, true);
            }
            return true;
        } catch (UnsupportedFlavorException ufe) {
        } catch (IOException ioe) {
        }
        return false;
    }

    // We only support file lists on FSTrees...
    @Override
    public boolean canImport(JComponent comp, DataFlavor[] transferFlavors) {
        for (DataFlavor transferFlavor : transferFlavors) {
            if (!transferFlavor.equals(DataFlavor.javaFileListFlavor)) {
                return false;
            }
        }
        return true;
    }
}
