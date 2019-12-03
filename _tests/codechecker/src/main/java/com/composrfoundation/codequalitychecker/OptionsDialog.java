package com.composrfoundation.codequalitychecker;

import java.awt.*;

import javax.swing.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import java.util.*;
import java.io.*;

public class OptionsDialog extends JDialog {

    JPanel panel1 = new JPanel();
    JTabbedPane jTabbedPane1 = new JTabbedPane();
    JButton closeBtn = new JButton();
    JButton cancelBtn = new JButton();
    JPanel environment = new JPanel();
    JPanel flags = new JPanel();
    JButton phpPathBtn = new JButton();
    JTextField phpPath = new JTextField();
    JLabel phpPathLabel = new JLabel();
    JButton basePathBtn = new JButton();
    JLabel basePathLabel = new JLabel();
    JTextField basePath = new JTextField();
    JButton textEditorPathBtn = new JButton();
    JLabel textEditorPathLabel = new JLabel();
    JTextField textEditorPath = new JTextField();
    VerticalFlowLayout verticalFlowLayout1 = new VerticalFlowLayout();
    JCheckBox api = new JCheckBox();
    JCheckBox todo = new JCheckBox();
    JCheckBox mixed = new JCheckBox();
    JCheckBox pedantic = new JCheckBox();
    JCheckBox security = new JCheckBox();
    JCheckBox manual_checks = new JCheckBox();
    JCheckBox spelling = new JCheckBox();
    JCheckBox codesniffer = new JCheckBox();
    JCheckBox eslint = new JCheckBox();

    public OptionsDialog(Frame owner, String title, boolean modal) {
        super(owner, title, modal);
        try {
            setDefaultCloseOperation(DISPOSE_ON_CLOSE);
            jbInit();
            pack();
        } catch (Exception exception) {
            exception.printStackTrace(System.out);
        }
    }

    public OptionsDialog() {
        this(new Frame(), "Options", false);
    }

    private void jbInit() throws Exception {
        this.setModal(true);
        this.setResizable(false);
        this.setTitle("Options");

        environment.setLayout(null);

        phpPathLabel.setText("PHP executable:");
        phpPathLabel.setBounds(new Rectangle(8, 52, 104, 22));
        environment.add(phpPathLabel);
        phpPath.setBounds(new Rectangle(114, 54, 228, 19));
        phpPath.setText(Main.phpPath);
        environment.add(phpPath);
        phpPathBtn.setBounds(new Rectangle(355, 53, 76, 22));
        phpPathBtn.setMargin(new Insets(0, 0, 0, 0));
        phpPathBtn.setActionCommand("phpPathBtn");
        phpPathBtn.setText("Browse");
        phpPathBtn.addActionListener(new OptionsDialog_phpPathBtn_actionAdapter(this));
        environment.add(phpPathBtn);

        basePathLabel.setText("Project path:");
        basePathLabel.setBounds(new Rectangle(9, 85, 104, 22));
        environment.add(basePathLabel);
        basePath.setBounds(new Rectangle(115, 87, 228, 19));
        basePath.setText(Main.basePath);
        environment.add(basePath);
        basePathBtn.setBounds(new Rectangle(356, 86, 76, 22));
        basePathBtn.setMargin(new Insets(0, 0, 0, 0));
        basePathBtn.setActionCommand("basePathBtn");
        basePathBtn.setText("Browse");
        basePathBtn.addActionListener(new OptionsDialog_basePathBtn_actionAdapter(this));
        environment.add(basePathBtn);

        textEditorPathLabel.setText("Text editor path:");
        textEditorPathLabel.setBounds(new Rectangle(8, 19, 104, 22));
        environment.add(textEditorPathLabel);
        textEditorPath.setBounds(new Rectangle(114, 21, 228, 19));
        textEditorPath.setText(Main.textEditorPath);
        environment.add(textEditorPath);
        textEditorPathBtn.setBounds(new Rectangle(355, 20, 76, 22));
        textEditorPathBtn.setMargin(new Insets(0, 0, 0, 0));
        textEditorPathBtn.setActionCommand("textEditorPathBtn");
        textEditorPathBtn.setText("Browse");
        textEditorPathBtn.addActionListener(new OptionsDialog_textEditorPathBtn_actionAdapter(this));
        environment.add(textEditorPathBtn);

        flags.setLayout(verticalFlowLayout1);

        api = new JCheckBox(api.getText(), Main.relay__api);
        api.setActionCommand("api");
        api.setText("Do API checks (recommended, esp as it helps determine type)");
        flags.add(api);

        todo = new JCheckBox(api.getText(), Main.relay__todo);
        todo.setActionCommand("todo");
        todo.setText("Flag any TODO-style comments");
        flags.add(todo);

        mixed = new JCheckBox(mixed.getText(), Main.relay__mixed);
        mixed.setActionCommand("mixed");
        mixed.setText("Flag variables that have no determinable type");
        flags.add(mixed);

        pedantic = new JCheckBox(pedantic.getText(), Main.relay__pedantic);
        pedantic.setActionCommand("pedantic");
        pedantic.setText("Show pedantic warnings (comment density, etc)");
        flags.add(pedantic);

        security = new JCheckBox(security.getText(), Main.relay__security);
        security.setActionCommand("security");
        security.setText("Flag security hotspots (e.g. query and exec)");
        flags.add(security);

        manual_checks = new JCheckBox(manual_checks.getText(), Main.relay__manual_checks);
        manual_checks.setActionCommand("manual_checks");
        manual_checks.setText("Flag areas that need special checking (e.g. file permissions)");
        flags.add(manual_checks);

        spelling = new JCheckBox(spelling.getText(), Main.relay__spelling);
        spelling.setActionCommand("spelling");
        spelling.setText("Spell checking (PHP must have pspell or enchant installed)");
        flags.add(spelling);

        codesniffer = new JCheckBox(codesniffer.getText(), Main.relay__codesniffer);
        codesniffer.setActionCommand("codesniffer");
        codesniffer.setText("Run 3rd party PHP CodeSniffer (must be in system path)");
        flags.add(codesniffer);

        eslint = new JCheckBox(eslint.getText(), Main.relay__eslint);
        eslint.setActionCommand("eslint");
        eslint.setText("Run 3rd party ESLint (must be installed via npm)");
        flags.add(eslint);

        jTabbedPane1.setBounds(new Rectangle(10, 13, 451, 287));
        jTabbedPane1.add(flags, "Flags");
        jTabbedPane1.add(environment, "Environment");

        closeBtn.setBounds(new Rectangle(389, 313, 71, 23));
        closeBtn.setMargin(new Insets(0, 0, 0, 0));
        closeBtn.setActionCommand("closeBtn");
        closeBtn.setText("Close");
        closeBtn.addActionListener(new OptionsDialog_closeBtn_actionAdapter(this));

        cancelBtn.setBounds(new Rectangle(308, 313, 71, 23));
        cancelBtn.setMargin(new Insets(0, 0, 0, 0));
        cancelBtn.setActionCommand("cancelBtn");
        cancelBtn.setText("Cancel");
        cancelBtn.addActionListener(new OptionsDialog_cancelBtn_actionAdapter(this));

        panel1.setLayout(null);
        getContentPane().add(panel1);
        panel1.add(jTabbedPane1);
        panel1.add(closeBtn);
        panel1.add(cancelBtn);
        panel1.setPreferredSize(new Dimension(475, 347));
    }

    public void closeBtn_actionPerformed(ActionEvent e) {
        Main.relay__api = api.isSelected();
        Main.relay__todo = todo.isSelected();
        Main.relay__mixed = mixed.isSelected();
        Main.relay__pedantic = pedantic.isSelected();
        Main.relay__security = security.isSelected();
        Main.relay__manual_checks = manual_checks.isSelected();
        Main.relay__spelling = spelling.isSelected();
        Main.relay__codesniffer = codesniffer.isSelected();
        Main.relay__eslint = eslint.isSelected();
        Main.basePath = basePath.getText();
        Main.textEditorPath = textEditorPath.getText();
        Main.phpPath = phpPath.getText();
        try {
            FileOutputStream out = new FileOutputStream(System.getProperty("user.dir") + File.separator + "codechecker.ini");
            Properties p = new Properties();
            p.put("relay__api", Main.relay__api ? "1" : "0");
            p.put("relay__todo", Main.relay__todo ? "1" : "0");
            p.put("relay__mixed", Main.relay__mixed ? "1" : "0");
            p.put("relay__pedantic", Main.relay__pedantic ? "1" : "0");
            p.put("relay__security", Main.relay__security ? "1" : "0");
            p.put("relay__manual_checks", Main.relay__manual_checks ? "1" : "0");
            p.put("relay__spelling", Main.relay__spelling ? "1" : "0");
            p.put("relay__codesniffer", Main.relay__codesniffer ? "1" : "0");
            p.put("relay__eslint", Main.relay__eslint ? "1" : "0");
            p.put("basePath", Main.basePath);
            p.put("textEditorPath", Main.textEditorPath);
            p.put("phpPath", Main.phpPath);
            p.store(out, null);
        } catch (IOException e2) {
            System.out.println(e2.toString());
        } // No saving then
        this.setVisible(false);
    }

    public void cancelBtn_actionPerformed(ActionEvent e) {
        this.setVisible(false);
    }

    public void phpPathBtn_actionPerformed(ActionEvent e) {
        String file = this.findFile(false);
        if (file != null) {
            phpPath.setText(file);
        }
    }

    public void basePathBtn_actionPerformed(ActionEvent e) {
        String file = this.findFile(true);
        if (file != null) {
            basePath.setText(file);
        }
    }

    public void textEditorPathBtn_actionPerformed(ActionEvent e) {
        String file = this.findFile(false);
        if (file != null) {
            textEditorPath.setText(file);
        }
    }

    public String findFile(boolean dirs) {
        JFileChooser fc = new JFileChooser();
        fc.setDialogTitle("Find File");

        // Choose only files, not directories
        fc.setFileSelectionMode(dirs ? JFileChooser.DIRECTORIES_ONLY : JFileChooser.FILES_ONLY);

        // Start in current directory
        fc.setCurrentDirectory(new File("."));

        // Now open chooser
        int result = fc.showOpenDialog(this);

        if (result == JFileChooser.CANCEL_OPTION) {
            return null;
        } else if (result == JFileChooser.APPROVE_OPTION) {

            File fFile = fc.getSelectedFile();
            String file_string = fFile.getAbsolutePath();

            if (file_string != null) {
                return file_string;
            }
        }

        return null;
    }
}

class OptionsDialog_phpPathBtn_actionAdapter implements ActionListener {

    private final OptionsDialog adaptee;

    OptionsDialog_phpPathBtn_actionAdapter(OptionsDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.phpPathBtn_actionPerformed(e);
    }
}

class OptionsDialog_basePathBtn_actionAdapter implements ActionListener {

    private final OptionsDialog adaptee;

    OptionsDialog_basePathBtn_actionAdapter(OptionsDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.basePathBtn_actionPerformed(e);
    }
}

class OptionsDialog_textEditorPathBtn_actionAdapter implements ActionListener {

    private final OptionsDialog adaptee;

    OptionsDialog_textEditorPathBtn_actionAdapter(OptionsDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.textEditorPathBtn_actionPerformed(e);
    }
}

class OptionsDialog_cancelBtn_actionAdapter implements ActionListener {

    private final OptionsDialog adaptee;

    OptionsDialog_cancelBtn_actionAdapter(OptionsDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.cancelBtn_actionPerformed(e);
    }
}

class OptionsDialog_closeBtn_actionAdapter implements ActionListener {

    private final OptionsDialog adaptee;

    OptionsDialog_closeBtn_actionAdapter(OptionsDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.closeBtn_actionPerformed(e);
    }
}
