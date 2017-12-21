<div class="container">

    <div class="row">

        <div class="col-md-12">

            <?php if($message): ?>
    <div class="alert alert-dismissible alert-info">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <strong>Heads up!</strong> This <a class="alert-link" href="#">alert needs your attention</a>, but it's not super important.
    </div>
<?php endif; ?>

<div class="well well-sm">
    <div class="row">

        <div class="col-md-2">
            <a href="/catalog/new_attribute" class="btn-sm btn-info">New Attribute</a>
        </div>
        <div class="col-md-10"></div>
    </div>

</div>

<br>

<table class="table table-striped table-hover ">
    <thead>
    <tr>
        <th>AttributeID</th>
        <th>Attribute Code</th>
        <th>Frontend input</th>
        <th>Required</th>
        <th>Note</th>
    </tr>
    </thead>

    <?php if($attributes->num_rows < '1'): ?>
        <tbody>
        <td>
            No results found
        </td>
        </tbody>
    <?php else: ?>
        <?php foreach($attributes->result() as $attribute): ?>
            <tbody>
            <tr>
                <td><?php echo $attribute->attribute_id; ?></td>
                <td><?php echo $attribute->attribute_code; ?></td>
                <td>
                    <?php if(empty($attribute->frontend_input)): ?>
                    Not defined
                    <?php else: ?>
                        <?php echo $attribute->frontend_input; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $attribute->is_required; ?></td>
                <td><?php echo $attribute->note; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    <?php endif; ?>


</table>

</div>

</div>


</div>