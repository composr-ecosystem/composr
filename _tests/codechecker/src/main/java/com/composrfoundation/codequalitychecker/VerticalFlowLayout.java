package com.composrfoundation.codequalitychecker;

import java.awt.LayoutManager;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.Container;
import java.awt.Insets;

/**
 * A vertical flow layout is similar to a flow layout but it layouts the
 * components vertically instead of horizontally.
 *
 * @author vassilidzuba
 * @created 24 mars 2001
 */
public final class VerticalFlowLayout implements LayoutManager, java.io.Serializable {

    int _halign;
    int _valign;
    int _hgap;
    int _vgap;

    /**
     * Description of the Field
     */
    public final static int TOP = 0;
    /**
     * Description of the Field
     */
    public final static int CENTER = 1;
    /**
     * Description of the Field
     */
    public final static int BOTTOM = 2;
    /**
     * Description of the Field
     */
    public final static int LEFT = 3;
    /**
     * Description of the Field
     */
    public final static int RIGHT = 4;

    /**
     * Constructor for the VerticalFlowLayout object
     */
    public VerticalFlowLayout() {
        this(LEFT, TOP, 5, 5);
    }

    /**
     * Constructor for the VerticalFlowLayout object
     *
     * @param halign Description of Parameter
     * @param valign Description of Parameter
     */
    public VerticalFlowLayout(int halign, int valign) {
        this(halign, valign, 5, 5);
    }

    /**
     * Constructor for the VerticalFlowLayout object
     *
     * @param halign Description of Parameter
     * @param valign Description of Parameter
     * @param hgap Description of Parameter
     * @param vgap Description of Parameter
     */
    public VerticalFlowLayout(int halign, int valign, int hgap, int vgap) {
        _hgap = hgap;
        _vgap = vgap;
        setAlignment(halign, valign);
    }

    /**
     * Sets the Alignment attribute of the VerticalFlowLayout object
     *
     * @param halign The new Alignment value
     * @param valign The new Alignment value
     */
    public void setAlignment(int halign, int valign) {
        _halign = halign;
        _valign = valign;
    }

    /**
     * Sets the Hgap attribute of the VerticalFlowLayout object
     *
     * @param hgap The new Hgap value
     */
    public void setHgap(int hgap) {
        _hgap = hgap;
    }

    /**
     * Sets the Vgap attribute of the VerticalFlowLayout object
     *
     * @param vgap The new Vgap value
     */
    public void setVgap(int vgap) {
        _vgap = vgap;
    }

    /**
     * Gets the Halignment attribute of the VerticalFlowLayout object
     *
     * @return The Halignment value
     */
    public int getHalignment() {
        return _halign;
    }

    /**
     * Gets the Valignment attribute of the VerticalFlowLayout object
     *
     * @return The Valignment value
     */
    public int getValignment() {
        return _valign;
    }

    /**
     * Gets the Hgap attribute of the VerticalFlowLayout object
     *
     * @return The Hgap value
     */
    public int getHgap() {
        return _hgap;
    }

    /**
     * Gets the Vgap attribute of the VerticalFlowLayout object
     *
     * @return The Vgap value
     */
    public int getVgap() {
        return _vgap;
    }

    /**
     * Adds a feature to the LayoutComponent attribute of the VerticalFlowLayout
     * object
     *
     * @param name The feature to be added to the LayoutComponent attribute
     * @param comp The feature to be added to the LayoutComponent attribute
     */
    @Override
    public void addLayoutComponent(String name, Component comp) {
    }

    /**
     * Description of the Method
     *
     * @param comp Description of Parameter
     */
    @Override
    public void removeLayoutComponent(Component comp) {
    }

    /**
     * Description of the Method
     *
     * @param target Description of Parameter
     * @return Description of the Returned Value
     */
    @Override
    public Dimension preferredLayoutSize(Container target) {
        synchronized (target.getTreeLock()) {
            Dimension dim = new Dimension(0, 0);
            int nmembers = target.getComponentCount();
            boolean firstVisibleComponent = true;

            for (int ii = 0; ii < nmembers; ii++) {
                Component m = target.getComponent(ii);
                if (m.isVisible()) {
                    Dimension d = m.getPreferredSize();
                    dim.width = Math.max(dim.width, d.width);
                    if (firstVisibleComponent) {
                        firstVisibleComponent = false;
                    } else {
                        dim.height += _vgap;
                    }
                    dim.height += d.height;
                }
            }
            Insets insets = target.getInsets();
            dim.width += insets.left + insets.right + _hgap * 2;
            dim.height += insets.top + insets.bottom + _vgap * 2;
            return dim;
        }
    }

    /**
     * Description of the Method
     *
     * @param target Description of Parameter
     * @return Description of the Returned Value
     */
    @Override
    public Dimension minimumLayoutSize(Container target) {
        synchronized (target.getTreeLock()) {
            Dimension dim = new Dimension(0, 0);
            int nmembers = target.getComponentCount();
            boolean firstVisibleComponent = true;

            for (int ii = 0; ii < nmembers; ii++) {
                Component m = target.getComponent(ii);
                if (m.isVisible()) {
                    Dimension d = m.getPreferredSize();
                    dim.width = Math.max(dim.width, d.width);
                    if (firstVisibleComponent) {
                        firstVisibleComponent = false;
                    } else {
                        dim.height += _vgap;
                    }
                    dim.height += d.height;
                }
            }
            Insets insets = target.getInsets();
            dim.width += insets.left + insets.right + _hgap * 2;
            dim.height += insets.top + insets.bottom + _vgap * 2;
            return dim;
        }
    }

    /**
     * Description of the Method
     *
     * @param target Description of Parameter
     */
    @Override
    public void layoutContainer(Container target) {
        synchronized (target.getTreeLock()) {
            Insets insets = target.getInsets();
            int maxheight = target.getHeight() - (insets.top + insets.bottom + _vgap * 2);
            int nmembers = target.getComponentCount();
            int y = 0;

            Dimension preferredSize = preferredLayoutSize(target);
            Dimension targetSize = target.getSize();

            switch (_valign) {
                case TOP:
                    y = insets.top;
                    break;
                case CENTER:
                    y = (targetSize.height - preferredSize.height) / 2;
                    break;
                case BOTTOM:
                    y = targetSize.height - preferredSize.height - insets.bottom;
                    break;
            }

            for (int i = 0; i < nmembers; i++) {
                Component m = target.getComponent(i);
                if (m.isVisible()) {
                    Dimension d = m.getPreferredSize();
                    m.setSize(d.width, d.height);

                    if ((y + d.height) <= maxheight) {
                        if (y > 0) {
                            y += _vgap;
                        }

                        int x = 0;
                        switch (_halign) {
                            case LEFT:
                                x = insets.left;
                                break;
                            case CENTER:
                                x = (targetSize.width - d.width) / 2;
                                break;
                            case RIGHT:
                                x = targetSize.width - d.width - insets.right;
                                break;
                        }

                        m.setLocation(x, y);

                        y += d.getHeight();

                    } else {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Description of the Method
     *
     * @return Description of the Returned Value
     */
    @Override
    public String toString() {
        String halign = "";
        switch (_halign) {
            case TOP:
                halign = "top";
                break;
            case CENTER:
                halign = "center";
                break;
            case BOTTOM:
                halign = "bottom";
                break;
        }
        String valign = "";
        switch (_valign) {
            case TOP:
                valign = "top";
                break;
            case CENTER:
                valign = "center";
                break;
            case BOTTOM:
                valign = "bottom";
                break;
        }
        return getClass().getName() + "[hgap=" + _hgap + ",vgap=" + _vgap + ",halign=" + halign + ",valign=" + valign + "]";
    }
}
